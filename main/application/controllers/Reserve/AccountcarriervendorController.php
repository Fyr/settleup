<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_System_SystemValues as SystemValues;

class Reserve_AccountcarriervendorController extends Zend_Controller_Action
{
    /** @var Application_Model_Entity_Accounts_Reserve_Vendor */
    protected $_entity;
    protected $_form;
    protected $_title = 'Vendor Reserve Account';
    protected $_carrierEntity;
    protected $_vendorEntity;

    public function init()
    {
        $this->_entity = new Application_Model_Entity_Accounts_Reserve_Vendor();
        $this->_form = new Application_Form_Account_Reserve_CarrierVendor();
    }

    public function indexAction()
    {
        $this->_forward('list');
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::RESERVE_ACCOUNT_CARRIER_VIEW) && !User::getCurrentUser(
        )->hasPermission(Permissions::RESERVE_ACCOUNT_VENDOR_VIEW)) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $id = $this->_getParam('id', 0);
        $entityId = $this->_getParam('entity', 0);

        $this->view->title = $this->_title;
        $this->view->form = $this->_form;
        $this->view->hideContractorSelector = true;

        $this->_vendorEntity = new Application_Model_Entity_Entity_Vendor();
        $this->_carrierEntity = new Application_Model_Entity_Entity_Carrier();

        if ($this->getRequest()->isPost()) {
            if (!User::getCurrentUser()->hasPermission(
                Permissions::RESERVE_ACCOUNT_CARRIER_MANAGE
            ) && !User::getCurrentUser()->hasPermission(Permissions::RESERVE_ACCOUNT_VENDOR_MANAGE)) {
                $this->_helper->redirector('index', 'settlement_index');
            }
            $post = $this->getRequest()->getPost();
            if ($this->_form->isValid($post)) {
                if ($id && !$entityId) {
                    $this->_entity->load($id);
                    if (!$this->_entity->checkPermissions()) {
                        $this->_helper->redirector('index');
                    }
                }
                $this->_entity->setData($this->_form->getValues());
                if ($entityId) {
                    $this->_entity->setData('id', '');
                    $this->_entity->setData('priority', '');
                }
                if (!$this->_entity->getId()) {
                    $this->_entity->setInitialBalance($this->_entity->getCurrentBalance());
                }
                $this->_entity->save();
                $this->_helper->redirector(
                    'index',
                    $this->_getParam('controller')
                );
            } else {
                $this->_form->populate($post);
            }
        } else {
            if ($entityId > 0) {
                $this->_vendorEntity->load($entityId, 'entity_id');
                if (!$this->_entity->checkPermissions(false)) {
                    $this->_helper->redirector('index');
                }
                $data = $this->_vendorEntity->getData();
                $this->_form->populate($data);
            } elseif ($id > 0) {
                $this->_entity->load($id);
                if (!$this->_entity->checkPermissions(false)) {
                    $this->_helper->redirector('index');
                }
                $data = $this->_entity->getData();
                $this->_form->populate($data);
            } else {
                $this->_setDefaultPopup();
            }
        }
        $this->_setDefaultPopup();
        $this->_form->configure();
        $this->view->showDeleteButton = $this->_entity->isDeletable();
    }

    public function listAction()
    {
        $this->view->gridModel = new Application_Model_Grid_ReserveAccount_CarrierVendor();
        $this->view->hideContractorSelector = true;
    }

    public function deleteAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $raEntity = $this->_entity->load($id)->getReserveAccountEntity();
        if ($this->_entity->checkPermissions()) {
            $raEntity->setDeleted(SystemValues::DELETED_STATUS);
            $raEntity->save();
            $this->_entity->reorderPriority();
        }
        $this->_helper->redirector('index');
    }

    public function multiactionAction()
    {
        $ids = explode(',', (string) $this->_getParam('ids'));
        foreach ($ids as $id) {
            $raEntity = $this->_entity->load($id)->getReserveAccountEntity();
            if ($this->_entity->checkPermissions()) {
                $raEntity->setDeleted(SystemValues::DELETED_STATUS);
                $raEntity->save();
                $this->_entity->reorderPriority();
            }
        }
        $this->_helper->redirector('index');
    }

    private function _setDefaultPopup()
    {
        $entityId = $this->_getParam('entity', 0);
        if (!$this->_form->id->getValue()) { // || !Application_Model_Entity_Accounts_User::getCurrentUser()->isOnboarding()
            if (!$entityId) {
                $user = User::getCurrentUser();
                $collections = [];
                if ($user->hasPermission(Permissions::RESERVE_ACCOUNT_VENDOR_MANAGE)) {
                    $collections['Vendor'] = $this->_vendorEntity->getCollection()->addConfiguredFilter(
                    )->addVisibilityFilterForUser(true);
                }

                if (!User::getCurrentUser()->isOnboarding() && $user->hasPermission(
                    Permissions::RESERVE_ACCOUNT_CARRIER_MANAGE
                )) {
                    $collections['Division'] = $this->_carrierEntity->getCollection()->addConfiguredFilter(
                    )->addVisibilityFilterForUser();
                }
                $this->view->popupSetup = [
                    'entity_name' => [
                        'gridTitle' => 'Select Vendor',
                        'destFieldName' => 'entity_id',
                        'idField' => 'entity_id',
                        'collections' => $collections,
                        'callbacks' => [
                            'name' => 'Application_Model_Grid_Callback_Text',
                            'tax_id' => 'Application_Model_Grid_Callback_Text',
                            'contact' => 'Application_Model_Grid_Callback_Text',
                            'short_code' => 'Application_Model_Grid_Callback_Text',
                        ],
                    ],
                ];
            } else {
                $user = User::getCurrentUser();
                $entity = Application_Model_Entity_Entity::staticLoad($entityId);
                if (($entity->isDivision() && (!$user->hasPermission(
                    Permissions::RESERVE_ACCOUNT_CARRIER_MANAGE
                ) || !$user->hasPermission(
                    Permissions::RESERVE_ACCOUNT_CARRIER_VIEW
                ))) || ($entity->isOnboarding() && (!$user->hasPermission(
                    Permissions::RESERVE_ACCOUNT_VENDOR_MANAGE
                ) || !$user->hasPermission(Permissions::RESERVE_ACCOUNT_VENDOR_VIEW)))) {
                    $this->_helper->redirector('index', 'settlement_index');
                }
                $this->_form->entity_id->setValue($entityId);
                $this->_form->entity_id_title->setAttrib('readonly', 'readonly');
                $this->view->popupSetup = [];
            }
        } else {
            $this->_form->entity_id_title->setAttrib('readonly', 'readonly');
            $this->view->popupSetup = [];
        }
    }
}
