<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_System_UserRoles as UserRoles;

class Users_IndexController extends Zend_Controller_Action
{
    use Application_Plugin_RedirectToIndex;

    /** @var Application_Model_Entity_Accounts_User */
    protected $_entity;
    protected $_form;
    protected $_title = 'Users';

    public function init()
    {
        $this->_entity = new User();
        $this->_form = new Application_Form_Account_User();
    }

    public function indexAction()
    {
        $this->_forward('list');
    }

    public function newAction()
    {
        $this->forward('edit');
    }

    public function editAction()
    {
        $this->view->title = $this->_title;
        $this->view->form = $this->_form;

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();

            $this->_form->populate($post);
            if ($this->_form->role_id->getValue() == UserRoles::SUPER_ADMIN_ROLE_ID
                || $this->_form->role_id->getValue() == UserRoles::MODERATOR_ROLE_ID
                || $this->_form->role_id->getValue() == UserRoles::GUEST_ROLE_ID) {
                $this->_form->entity_id_title->setRequired(false);
            }
            $this->_form->appendSubforms($post['contacts']);
            $isRequired = false;
            if (in_array(
                $this->_form->role_id->getValue(),
                [UserRoles::VENDOR_ROLE_ID, UserRoles::CONTRACTOR_ROLE_ID]
            )) {
                $this->_form->entity_id_title->setRequired(false);
                $this->_form->entity_id->setRequired(false);
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
                    if (!User::getCurrentUser()->isAdmin()) {
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
                            $this->view->popupSetup = $this->getPopups();

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

                if ($this->_form->role_id->getValue() != UserRoles::SUPER_ADMIN_ROLE_ID
                    && $this->_form->role_id->getValue() != UserRoles::MODERATOR_ROLE_ID
                    && $this->_form->role_id->getValue() != UserRoles::GUEST_ROLE_ID) {
                    if ($this->_form->role_id->getValue() != UserRoles::CARRIER_ROLE_ID) {
                        $this->_form->saveEntities($this->_entity);
                    }
                    $this->_form->saveSubforms($this->_entity->getId(), 'user_id');
                    if ($isNewUser) {
                        $this->_entity->updateRestData();
                    }
                }

                if (!$isNewUser) {
                    $this->_entity->updateRestData();
                    if ($this->_getParam('redirect')) {
                        if (User::getCurrentUser()->isContractor()) {
                            $this->redirect('/reporting_index');
                        } elseif (User::getCurrentUser()->isAdmin()) {
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
                if (in_array(
                    $this->_form->role_id->getValue(),
                    [UserRoles::VENDOR_ROLE_ID, UserRoles::CONTRACTOR_ROLE_ID]
                )) {
                    $this->_form->entity_id_title->setRequired(true);
                    $this->_form->entity_id->setRequired(true);
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
            $this->_form->appendEntities($this->_entity->getEntities());
            $this->_form->appendSubforms($this->_entity->getAllContacts());
        }
        $this->_form->configure();
        $this->view->popupSetup = $this->getPopups();
        $this->view->popupUserEntity = $this->getUserEntityPopup();
    }

    public function listAction()
    {
        $user = User::getCurrentUser();
        if ($user->isAdmin() || ($user->isCarrier() && $user->hasPermission(Permissions::PERMISSIONS_MANAGE))) {
            $this->view->gridModel = new Application_Model_Grid_Entity_User();
        } else {
            $this->redirect('/');
        }
    }

    public function deleteAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $this->_entity->load($id);
        if ($this->_entity->checkPermissions()) {
            $this->_entity->setDeleted(Application_Model_Entity_System_SystemValues::DELETED_STATUS)->save();
        }
        $this->_helper->redirector('index');
    }

    public function multiactionAction()
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

    public function addselecteditemsAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $contractorsId = $this->getRequest()->getParam('selectedItemsId');
            $this->_entity->addContractors($contractorsId);
            $this->_forward('list', null, null, ['isAjax' => 'true']);
        }
    }

    public function getPopups()
    {
        $user = User::getCurrentUser();
        // TODO review and simplify difficult condition
        if (!$this->_form->id->getValue() || $user->isAdmin() || ($user->isCarrier() && !$this->_form->id->getValue(
        ) && ($user->hasPermission(
            Permissions::VENDOR_USER_CREATE
        ) || $user->hasPermission(Permissions::CONTRACTOR_USER_CREATE)))) {
            $popups = [
                'entity' => [
                    'filterable' => true,
                    'gridTitle' => 'Select entity',
                    'destFieldName' => 'entity_id',
                    'idField' => 'entity_id',
                    'collections' => [
                        'Division' => new Application_Model_Grid_User_Carrier(),
                    ],
                    'showClearButton' => false,
                ],
            ];
            if ($user->isCarrier()) {
                unset($popups['entity']['collections']['Division']);
            }

            return $popups;
        } else {
            return [];
        }
    }

    public function getUserEntityPopup()
    {
        $user = User::getCurrentUser();
        if (!$this->_form->id->getValue() || $user->isAdmin() || ($user->isCarrier() && ($user->hasPermission(
            Permissions::VENDOR_USER_CREATE
        ) || $user->hasPermission(Permissions::CONTRACTOR_USER_CREATE)))) {
            $vendor = new Application_Model_Grid_User_EntityVendor();
            //            $vendor->setData('row_data', array('carrier_id' => 'carrier-id', 'carrier_name' => 'carrier-name'));
            //            $vendor->setHeader($header);
            $contractor = new Application_Model_Grid_User_EntityContractor();
            //            $contractor->setData('row_data', array('carrier_id' => 'carrier-id', 'carrier_name' => 'carrier-name'));
            $popups = [
                'entity' => [
                    'filterable' => true,
                    'gridTitle' => 'Select Entity',
                    'destFieldName' => 'multiple_entity_id',
                    'idField' => 'entity_id',
                    'collections' => [
                        'Vendor' => $vendor,
                        'Contractor' => $contractor,
                    ],
                ],
            ];
            if ($user->isCarrier()) {
                if (!$user->hasPermission(Permissions::VENDOR_USER_CREATE)) {
                    unset($popups['entity']['collections']['Vendor']);
                } elseif (!$user->hasPermission(Permissions::CONTRACTOR_USER_CREATE)) {
                    unset($popups['entity']['collections']['Contractor']);
                }
            }

            return $popups;
        } else {
            return [];
        }
    }

    public function permissionsAction()
    {
        $user = User::getCurrentUser();
        $id = $this->_getParam('id', 0);
        if (($user->isAdmin() || ($user->isCarrier() && $user->hasPermission(
            Permissions::PERMISSIONS_MANAGE
        ))) && $id != $user->getId()) {
            $entity = new Permissions();

            $entity->load($id, 'user_id');
            if (!$entity->getUserId()) {
                $entity->setUserId(0);
            }

            $this->_entity->load($id);
            if (!$this->_entity->getId() || !$this->_entity->checkPermissions()) {
                $this->_redirect('/settlement_index');
            }

            if ($this->_entity->isCarrier()) {
                $this->view->form = $form = new Application_Form_Entity_CarrierPermissions();
                $this->view->title = "Carrier Permissions";
            } elseif ($this->_entity->isVendor()) {
                $this->view->form = $form = new Application_Form_Entity_VendorPermissions();
                $this->view->title = "Vendor Permissions";
            } else {
                $this->_redirect('/settlement_index');
            }

            if ($this->getRequest()->isPost()) {
                $post = $this->getRequest()->getPost();
                $form->populate($post);
                if ($user->isCarrier()) {
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
