<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Carrier as Division;
use Application_Model_Entity_Entity_UserAuthProviders as UserAuthProviders;
use Application_Model_Entity_System_UserRoles as UserRoles;
use Application_Service_Azure_AuthProvider as AuthProvider;

class AuthController extends Zend_Controller_Action
{
    use Application_Model_Entity_EntitySyncTrait;

    final public const SESSION_SSO_NAMESPACE = 'sso';

    public function loginAction()
    {
        $form = new Application_Form_Auth_Login();
        $this->view->form = $form;
        $message = 'Your email or password does not match our records. Please verify and try again.';
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if ($form->isValid($post)) {
                try {
                    $user = (new User())->authUser(
                        $form->getValue('email'),
                        $form->getValue('password')
                    );
                    if ($user) {
                        $user->checkLastSelectedDivision();
                        $this->redirect('/');

                        return $this;
                    }
                } catch (AccessDeniedException $e) {
                    $message = 'User found but access denied, ' . $e->getMessage();
                } catch (Exception $e) {
                    $message = $e->getMessage();
                }
                $this->view->error = $message;
            } else {
                $form->populate($post);
            }
        }
    }

    public function logoutAction()
    {
        User::getCurrentUser()->logout();
        $this->redirect('/');
    }

    public function forgotAction()
    {
        $form = new Application_Form_Auth_Forgot();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if ($form->isValid($post)) {
                if (User::forgotPassword($form->email->getValue())) {
                    $this->view->message = 'We sent to you email with instructions. Please, check it.';
                } else {
                    $this->view->message = 'Something goes wrong. Please, try again.';
                }
            } else {
                $form->populate($post);
            }
        }
    }

    public function resetAction()
    {
        $form = new Application_Form_Auth_Reset();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if ($form->isValid($post)) {
                if (User::resetPassword(
                    $this->_getParam('email'),
                    $this->_getParam('hash'),
                    $form->password->getValue()
                )) {
                    $this->view->message = 'Password has been successfully changed. Now you can login via new password.';
                } else {
                    $this->redirect('/');
                }
            } else {
                $form->populate($post);
            }
        } elseif (!User::checkHash($this->_getParam('email'), $this->_getParam('hash'))) {
            $this->redirect('/');
        }
    }

    protected function _checkAccess()
    {
        if (User::getCurrentUser()->isLoggedIn()) {
            $this->redirect('/');
        }
    }

    /**
     * @throws Zend_Session_Exception
     */
    public function ssoLoginAction(): void
    {
        $provider = (new AuthProvider())->getProvider();
        $authorizationUrl = $provider->getAuthorizationUrl(['scope' => $provider->scope]);
        $this->getSsoSessionStorage()->__set('state', $provider->getState());
        $this->redirect($authorizationUrl);
    }

    /**
     * @throws Zend_Session_Exception
     */
    public function ssoAction(): void
    {
        $request = $this->getRequest();
        if ($authorizationCode = $request->getParam('code')) {
            $sessionStorage = $this->getSsoSessionStorage();
            if ($request->getParam('state') !== $sessionStorage->state) {
                $this->redirect('/auth/login');
            }
            $sessionStorage->__unset('state');

            try {
                $authProvider = new AuthProvider();
                $userAdData = $authProvider->getUserAdData($authorizationCode);
                $roleId = $userAdData['roleId'];
                $user = (new User())->getByEmail($userAdData['email']);
                if ($user->isEmpty()) {
                    $user = (new User())->setData([
                        'role_id' => $roleId,
                        'email' => $userAdData['email'],
                        'name' => $userAdData['name'],
                        'password' => $userAdData['sub'],
                    ]);
                    $user->save(true);
                    $user->createSsoRestData();
                }

                $authProvider = (new UserAuthProviders())->getByUserId($user->getId());
                if ($authProvider->isEmpty()) {
                    $authProvider = (new UserAuthProviders())->create([
                        'providerId' => $userAdData['sub'],
                        'providerType' => UserAuthProviders::PROVIDER_TYPE_AZURE,
                        'userId' => $user->getId(),
                        'adData' => $userAdData,
                    ]);
                }

                $entity = new Division();
                $newEntityIds = [];
                if (UserRoles::GUEST_ROLE_ID !== $roleId) {
                    foreach ($userAdData['divisions'] as $divisionCode) {
                        $entity = $entity->load($divisionCode, 'short_code');
                        if ($entity->isEmpty()) {
                            $this->getLogger()->err('Sso err division not found by divisionCode: ' . $divisionCode);
                            continue;
                        }
                        $newEntityIds[] = $entity->getEntityId();
                    }
                }
                $this->syncEntities($user, $newEntityIds);

                if (!$user->getLastSelectedCarrier() && !$entity->isEmpty()) {
                    $user->setLastSelectedCarrier($entity->getId());
                    $user->setEntityId($entity->getEntityId());
                    $user->save();
                }

                if ((int) $user->getRoleId() !== $roleId) {
                    $this->getLogger()->info('Sso change user role ' . $user->getRoleId() . ' to ' . $roleId);
                    $user->setRoleId($roleId);
                    if (UserRoles::GUEST_ROLE_ID === $roleId) {
                        $user->setLastSelectedCarrier(null);
                        $user->setEntityId(null);
                    }
                    $user->save();
                    $authProvider->setAdData($userAdData);
                    $authProvider->save();
                    $this->isNeedUpdateRestData = true;
                }

                $authUser = (new User())->authUser(
                    $user->getEmail(),
                    $authProvider->getProviderId()
                );
                if ($authUser) {
                    if ($this->isNeedUpdateRestData) {
                        $user->updateRestData();
                    }
                    $user->checkLastSelectedDivision();
                    $this->redirect('/');
                }
            } catch (Throwable $e) {
                $this->getLogger()->err('Sso exception: ' . $e->getMessage());
            }
        }

        if ($request->getParam('error')) {
            $this->getLogger()->err('Sso error: ' . $request->getParam('error'));
            $this->getLogger()->err('Sso error description: ' . $request->getParam('error_description'));
        }

        $this->redirect('/auth/login');
    }

    protected function getLogger(): Zend_Log
    {
        return Zend_Registry::get('logger');
    }

    protected function getSsoSessionStorage(): Zend_Session_Namespace
    {
        return new Zend_Session_Namespace(self::SESSION_SSO_NAMESPACE);
    }
}
