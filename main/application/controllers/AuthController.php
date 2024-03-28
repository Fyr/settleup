<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_UserAuthProviders as UserAuthProviders;
use Application_Model_Entity_System_UserRoles as UserRoles;
use Application_Service_Azure_AuthProvider as AuthProvider;
use TheNetworg\OAuth2\Client\Token\AccessToken;

class AuthController extends Zend_Controller_Action
{
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

    public function ssoLoginAction(): void
    {
        $provider = (new AuthProvider())->getProvider();
        $authorizationUrl = $provider->getAuthorizationUrl(['scope' => $provider->scope]);
        $this->getSsoSessionStorage()->state = $provider->getState();
        $this->redirect($authorizationUrl);
    }

    public function ssoAction(): void
    {
        $request = $this->getRequest();
        $sessionStorage = $this->getSsoSessionStorage();
        if ($request->getParam('code') && $request->getParam('state')) {
            if ($request->getParam('state') !== $sessionStorage->state) {
                $this->redirect('/auth/login');
            }
            $sessionStorage->unsetAll();
            $authProvider = new AuthProvider();
            $provider = $authProvider->getProvider();

            try {
                /** @var AccessToken $token */
                $token = $provider->getAccessToken('authorization_code', [
                    'scope' => $provider->scope,
                    'code' => $request->getParam('code'),
                ]);
                $userAdInfo = $provider->validateAccessToken($token->getToken());

                /** @var AccessToken $newToken */
                $newToken = $provider->getAccessToken('refresh_token', [
                    'scope' => $authProvider->getScopeGroupRead(),
                    'refresh_token' => $token->getRefreshToken(),
                ]);
                $userAdRoles = $authProvider->getUserRoles($newToken);
                $this->getLogger()->info('Sso found roles info: ' . json_encode($userAdRoles, JSON_THROW_ON_ERROR));

                $appRoles = $authProvider->getArrayAppRoles($newToken);
                $this->getLogger()->info('Sso app roles info: ' . json_encode($appRoles, JSON_THROW_ON_ERROR));

                $email = strtolower($userAdInfo['preferred_username'] ?? '');
                $adData = $authProvider->getUserAuthData($userAdRoles, $appRoles, $email);
                $roleId = $adData['roleId'];
                $user = (new User())->getByEmail($email);
                if ($user->isEmpty()) {
                    $user = (new User())->setData([
                        'role_id' => $roleId,
                        'email' => $email,
                        'name' => $userAdInfo['name'],
                        'password' => $userAdInfo['sub'],
                    ]);
                    $user->save(true);
                    $user->createSsoRestData();
                }

                $authProvider = (new UserAuthProviders())->getByUserId($user->getId());
                if ($authProvider->isEmpty()) {
                    $authProvider = (new UserAuthProviders())->create([
                        'providerId' => $userAdInfo['sub'],
                        'providerType' => UserAuthProviders::PROVIDER_TYPE_AZURE,
                        'userId' => $user->getId(),
                        'adData' => $adData,
                    ]);
                }

                if (!$user->getLastSelectedCarrier() && $adData['divisionCode']) {
                    $entity = (new Application_Model_Entity_Entity_Carrier())
                        ->load($adData['divisionCode'], 'short_code');
                    if ($entity->getEntityId()) {
                        $user->setLastSelectedCarrier($entity->getId());
                        $user->setEntityId($entity->getEntityId());
                        $user->save();
                    } else {
                        $this->getLogger()->err('Sso err division not found by divisionCode: ' . $adData['divisionCode']);
                    }
                }

                if ((int) $user->getRoleId() !== $roleId) {
                    $this->getLogger()->info('Sso change user role ' . $user->getRoleId() . ' to ' . $roleId);
                    $user->setRoleId($roleId);
                    if (UserRoles::GUEST_ROLE_ID === $roleId) {
                        $user->setLastSelectedCarrier(null);
                        $user->setEntityId(null);
                    }
                    $user->save();
                    $authProvider->setAdData($adData);
                    $authProvider->save();
                }

                $authUser = (new User())->authUser(
                    $user->getEmail(),
                    $authProvider->getProviderId()
                );
                if ($authUser) {
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
