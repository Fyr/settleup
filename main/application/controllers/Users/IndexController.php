<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_System_UserRoles as UserRoles;

class Users_IndexController extends Zend_Controller_Action
{
    use Application_Plugin_RedirectToIndex;

    protected User $_entity;
    protected Application_Form_Account_User $_form;
    protected $_title = 'Users';

    public function init(): void
    {
        $this->_entity = new User();
        $this->_form = new Application_Form_Account_User();
    }

    public function indexAction(): void
    {
        $this->forward('list');
    }

    public function newAction(): void
    {
        $this->forward('edit');
    }

    public function editAction(): void
    {
        $this->view->title = $this->_title;
        $this->view->form = $this->_form;
        $currentUser = User::getCurrentUser();

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();

            $this->_form->populate($post);
            $this->_form->appendSubforms($post['contacts']);
            $isRequired = false;
            if (in_array($this->_form->role_id->getValue(), [
                UserRoles::MANAGER_ROLE_ID,
                UserRoles::SPECIALIST_ROLE_ID,
                UserRoles::ONBOARDING_ROLE_ID,
            ])) {
                $isRequired = true;
            }
            $this->_form->appendEntities($post['entities'], $isRequired);

            if ($this->_form->isValid($post)) {
                $passwordChanged = false;
                $data = $this->_form->getValues();
                if ($data['entity_id'] == '') {
                    $data['entity_id'] = null;
                }
                $isNewUser = true;
                if ($id = $data['id']) {
                    $this->_entity->load($id);
                    if (!$currentUser->isAdminOrSuperAdmin()) {
                        if (!$this->_entity->checkPermissions()) {
                            $this->_helper->redirector('index');
                        }
                        unset($data['role_id']);
                        unset($data['email']);
                        unset($data['entity_id']);
                    }

                    if ($this->_entity->getId()) {
                        unset($data['password']);
                        $isNewUser = false;
                    }
                    if ($data['old_password'] && $data['new_password']) {
                        if (!$this->_entity->setOldPassword($data['old_password'])->checkOldPassword()) {
                            $this->_form->old_password->addError('Incorrect password!');
                            $this->_form->populate($post);
                            $this->_form->configure();
                            $this->view->popupUserEntity = $this->getUserEntityPopup();

                            return;
                        } else {
                            $data['password'] = $data['new_password'];
                            $passwordChanged = true;
                        }
                    }
                }
                $this->_entity->addData($data);
                $this->_entity->save();

                if ($passwordChanged) {
                    $this->_entity->setNewPassword($data['new_password']);
                }

                if (in_array($this->_form->role_id->getValue(), [
                    UserRoles::MANAGER_ROLE_ID,
                    UserRoles::SPECIALIST_ROLE_ID,
                    UserRoles::ONBOARDING_ROLE_ID,
                ])) {
                    $this->_form->saveEntities($this->_entity);
                    $this->_form->saveSubforms($this->_entity->getId(), 'user_id');
                    if ($isNewUser) {
                        $this->_entity->updateRestData();
                    }
                }

                if (!$isNewUser) {
                    $this->_entity->updateRestData();
                    if ($this->_getParam('redirect')) {
                        if ($currentUser->isAdminOrSuperAdmin()) {
                            $this->redirect('/users_index');
                        } else {
                            $this->redirect('/settlement_index');
                        }
                    } else {
                        $this->redirectToIndex();
                    }
                } else {
                    $this->_helper->redirector(
                        'edit',
                        'users_index',
                        null,
                        ['id' => $this->_entity->getId()]
                    );
                }
            } else {
                $this->_form->populate($post);
                if (in_array($this->_form->role_id->getValue(), [
                    UserRoles::MANAGER_ROLE_ID,
                    UserRoles::SPECIALIST_ROLE_ID,
                    UserRoles::ONBOARDING_ROLE_ID,
                ])) {
                    foreach ($this->_form->getSubForms() as $name => $subform) {
                        if (preg_match('/^entity-subform-\S*/', (string) $name)) {
                            $subform->entity_id_title->setRequired(false);
                        }
                    }
                }
            }
        } else {
            $id = $this->_getParam('id', 0);
            if ($id > 0) {
                $this->_form->populate($this->_entity->load($id)->getDefaultValues()->getData());
                if (!$this->_entity->checkPermissions()) {
                    $this->_helper->redirector('index');
                }
            }
            $isReadonly = true;
            if ($currentUser->isSuperAdmin()) {
                $isReadonly = false;
            }
            $this->_form->appendEntities($this->_entity->getEntities(), true, $isReadonly);
            $this->_form->appendSubforms($this->_entity->getAllContacts());
        }
        $this->_form->configure();
        $this->view->popupUserEntity = $this->getUserEntityPopup();
    }

    public function listAction(): void
    {
        $user = User::getCurrentUser();
        if ($user->isAdminOrSuperAdmin() || $user->isManager()) {
            $this->view->gridModel = new Application_Model_Grid_Entity_User();
        } else {
            $this->redirect('/');
        }
    }

    public function deleteAction(): void
    {
        $id = (int)$this->getRequest()->getParam('id');
        $this->_entity->load($id);
        if ($this->_entity->checkPermissions()) {
            $this->_entity->setDeleted(Application_Model_Entity_System_SystemValues::DELETED_STATUS)->save();
        }
        $this->_helper->redirector('index');
    }

    public function multiactionAction(): void
    {
        $ids = explode(',', (string) $this->_getParam('ids'));
        foreach ($ids as $id) {
            $this->_entity->load((int)$id);
            if ($this->_entity->checkPermissions()) {
                $this->_entity->setDeleted(Application_Model_Entity_System_SystemValues::DELETED_STATUS)->save();
            }
        }
        $this->_helper->redirector('index');
    }

    public function addselecteditemsAction(): void
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $contractorsId = $this->getRequest()->getParam('selectedItemsId');
            $this->_entity->addContractors($contractorsId);
            $this->_forward('list', null, null, ['isAjax' => 'true']);
        }
    }

    public function getUserEntityPopup(): array
    {
        $user = User::getCurrentUser();
        if ($user->isSuperAdmin()) {
            return [
                'entity' => [
                    'filterable' => true,
                    'gridTitle' => 'Select Entity',
                    'destFieldName' => 'multiple_entity_id',
                    'idField' => 'entity_id',
                    'collections' => [
                        'Division' => new Application_Model_Grid_User_Carrier(),
                    ],
                ],
            ];
        }

        return [];
    }

    public function permissionsAction()
    {
        $user = User::getCurrentUser();
        $id = $this->_getParam('id', 0);
        if (($user->isAdminOrSuperAdmin()
                || ($user->isManager() && $user->hasPermission(Permissions::PERMISSIONS_MANAGE)))
            && $id != $user->getId()) {
            $entity = new Permissions();

            $entity->load($id, 'user_id');
            if (!$entity->getUserId()) {
                $entity->setUserId(0);
            }

            $this->_entity->load($id);
            if (!$this->_entity->getId() || !$this->_entity->checkPermissions()) {
                $this->_redirect('/settlement_index');
            }

            if ($this->_entity->isManager()) {
                $this->view->form = $form = new Application_Form_Entity_ManagerPermissions();
                $this->view->title = "Manager Permissions";
            } elseif ($this->_entity->isSpecialist()) {
                $this->view->form = $form = new Application_Form_Entity_SpecialistPermissions();
                $this->view->title = "Settlements Specialist Permissions";
            } elseif ($this->_entity->isOnboarding()) {
                $this->view->form = $form = new Application_Form_Entity_OnboardingPermissions();
                $this->view->title = "Settlements Onboarding Specialist Permissions";
            } else {
                $this->_redirect('/settlement_index');
            }

            if ($this->getRequest()->isPost()) {
                $post = $this->getRequest()->getPost();
                $form->populate($post);
                if ($user->isManager()) {
                    if (isset($post['permissions_manage'])) {
                        unset($post['permissions_manage']);
                    }
                    //                    if (!$user->hasPermission(Application_Model_Entity_Entity_Permissions::CONTRACTOR_VENDOR_AUTH_MANAGE) && isset($post['contractor_vendor_auth_manage'])) {
                    //                        unset($post['contractor_vendor_auth_manage']);
                    //                    }
                }
                if ($form->isValid($post)) {
                    if ($form->id->getValue()) {
                        $entity->load(
                            $form->id->getValue()
                        );
                    }
                    $entity->addData($form->getValues())->save();
                    if ($this->_getParam('redirect')) {
                        $this->_redirect('/settlement_index');
                    } else {
                        $this->redirectToIndex();
                    }
                } else {
                    $form->populate($post);
                }
            } else {
                if (!$entity->getId()) {
                    $entity->setUserId($id)->save();
                }
                $form->populate($entity->getData());
            }
        } else {
            $this->_redirect('/settlement_index');
        }
    }
}
