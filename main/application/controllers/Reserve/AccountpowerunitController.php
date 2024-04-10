<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Powerunit_Powerunit as Powerunit;
use Application_Model_Entity_System_ReserveAccountType as ReserveAccountType;
use Application_Model_Grid_ReserveAccount_Powerunit as PowerunitGrid;

class Reserve_AccountpowerunitController extends Zend_Controller_Action
{
    /** @var Application_Model_Entity_Accounts_Reserve_Powerunit */
    protected $_entity;
    protected $_form;
    protected $_title = 'Power Unit Reserve Account';
    protected $_powerunitEntity;
    protected $_vendorEntity;
    protected $_carrierEntity;

    public function init()
    {
        $this->_entity = new Application_Model_Entity_Accounts_Reserve_Powerunit();
        $this->_form = new Application_Form_Account_Reserve_Powerunit();
        $this->_powerunitEntity = new Powerunit();
    }

    public function indexAction()
    {
        $this->_forward('list');
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function viewAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->view->title = $this->_title;
        $this->view->form = $this->_form;
        // $this->view->hidePowerunitSelector = true;
        $user = User::getCurrentUser();
        if ($user->isOnboarding() || $user->isSpecialist()) {
            $this->view->form->readonly();
        }
        // $raAccountEntity = new Application_Model_Entity_Accounts_Reserve_Vendor();
        // $this->_vendorEntity = new Application_Model_Entity_Entity_Vendor();
        // $this->_carrierEntity = new Application_Model_Entity_Entity_Carrier();
        // $this->view->setupFields = json_encode($raAccountEntity->getResource()->getSetupFields(), JSON_THROW_ON_ERROR);
        // $this->view->popupSetup = [
        //     'powerunit_name' => [
        //         'gridTitle' => 'Select powerunit',
        //         'titleField' => 'code',
        //         'destFieldName' => 'entity_id',
        //         'idField' => 'id',
        //         'collections' => [
        //             $this->_powerunitEntity->getCollection()->getEmptyCollection(),
        //         ],
        //         'callbacks' => [
        //             'name' => Application_Model_Grid_Callback_Text::class,
        //             'tax_id' => Application_Model_Grid_Callback_Text::class,
        //             'contact' => Application_Model_Grid_Callback_Text::class,
        //             'short_code' => Application_Model_Grid_Callback_Text::class,
        //         ],
        //     ],
        //     'reserve_account_vendor_id_title' => [
        //         'gridTitle' => 'Select reserve account',
        //         'destFieldName' => 'reserve_account_vendor_id',
        //         'idField' => 'id',
        //         'collections' => [
        //             $raAccountEntity->getCollection()->getEmptyCollection(),
        //         ],
        //         'callbacks' => [
        //             'priority' => Application_Model_Grid_Callback_Priority::class,
        //         ],
        //     ],
        //     'vendor_id_title' => $this->_getCarrierVendorPopup(),
        // ];

        if ($this->getRequest()->isPost()) {
            if (User::getCurrentUser()->isOnboarding()) {
                $this->_helper->redirector('reporting_index');
            }
            $post = $this->getRequest()->getPost();
            if ($post['account_type'] == ReserveAccountType::ESCROW_ACCOUNT) {
                $this->_form->min_balance->setRequired();
            }
            if ($this->_form->isValid($post)) {
                $this->_entity->setData($this->_form->getValues());
                if (!$this->_form->id->getValue()) {
                    $this->_entity->unsPriority();
                }
                if ($this->_form->start_date->getValue()) {
                    $this->_entity->setCreatedAt($this->_form->start_date->getValue());
                    $this->_entity->changeDatetimeFormat(['created_at']);
                }
                $this->_entity->save();
                if (!$this->_form->id->getValue()) {
                    $this->_entity->setNewPriority();
                    $this->_entity->addToHistory();
                }
                if ($back = $this->getRequest()->getParam('back')) {
                    $this->_redirect($back);
                }
                $this->_helper->redirector('index', $this->_getParam('controller'));
            } else {
                $this->_form->populate($post);
                $this->_entity->setId($this->_form->id->getValue());
            }
        } else {
            $id = $this->_getParam('id', 0);
            if ($id > 0) {
                $this->_entity->load($id);
                if (!$this->_entity->checkPermissions()) {
                    $this->_helper->redirector('index');
                }
                $data = $this->_entity->getData();
                $this->_form->populate($data);
            }
            // else {
            //     if ($powerunitId = $this->_getParam('powerunit_id')) {
            //         $this->_entity->setPowerunitId($powerunitId);
            //         $this->_form->powerunit_id->setValue($powerunitId);
            //         $this->_form->entity_id_title->setValue($this->_entity->getPowerunit()->getCode());
            //     }
            // }
        }
        // $this->_setDefaultPopup();
        $this->_form->configure();
        $this->view->showDeleteButton = $this->_entity->isDeletable();
        $this->view->raAccountTypes = (new ReserveAccountType())->getAccountTypeOptions();
    }

    public function listAction()
    {
        $this->view->gridModel = new PowerunitGrid();
    }

    public function deleteAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $raEntity = $this->_entity->load($id);
        if ($this->_entity->checkPermissions()) {
            $raEntity->setDeleted(Application_Model_Entity_System_SystemValues::DELETED_STATUS);
            $raEntity->save();
            $raEntity->deleteHistory();
            $this->_entity->reorderPriority();
            // $this->_entity->getResource()->updateReserveAccountVendorInitialBalance($raEntity->getId());
        }
        if ($back = $this->getRequest()->getParam('back')) {
            $this->_redirect($back);
        }
        $this->_helper->redirector('index');
    }

    // public function updateracollectionAction()
    // {
    //     if ($this->getRequest()->isXmlHttpRequest()) {
    //         $vendorId = $this->_getParam('vendorEntityId');
    //         $raVendorEntity = new Application_Model_Entity_Accounts_Reserve_Vendor();
    //         $this->view->popupSetup = [
    //             'reserve_account_vendor_id_title' => [
    //                 'gridTitle' => 'Select reserve account',
    //                 'destFieldName' => 'reserve_account_vendor_id',
    //                 'idField' => 'id',
    //                 'collections' => [
    //                     $raVendorEntity->getCollection()->addFilter('entity_id', $vendorId)->addNonDeletedFilter(),
    //                 ],
    //                 'callbacks' => [
    //                     'priority' => 'Application_Model_Grid_Callback_Priority',
    //                 ],
    //             ],
    //         ];
    //         $this->_helper->layout->disableLayout();
    //     } else {
    //         $this->_helper->redirector('index');
    //     }
    // }

    // public function updaterasetupAction()
    // {
    //     if ($this->getRequest()->isXmlHttpRequest()) {
    //         $raVendorId = $this->_getParam('raId');
    //         $reserveAccountEntity = new Application_Model_Entity_Accounts_Reserve_Vendor();
    //         $data = $reserveAccountEntity->load($raVendorId)->getSetupData();
    //         $data = array();
    //         $data['min_balance'] = number_format($data['min_balance'], 2);
    //         $data['contribution_amount'] = number_format($data['contribution_amount'], 2);

    //         return $this->_helper->json->sendJson($data);
    //     } else {
    //         $this->_helper->redirector('index');
    //     }
    // }

    // public function updatecarriervendorAction()
    // {
    //     if ($this->getRequest()->isXmlHttpRequest()) {
    //         $powerunitId = $this->_getParam('powerunit-id');
    //         $this->_powerunitEntity = (new Application_Model_Entity_Powerunit_Powerunit())->load(
    //             $powerunitId,
    //             'entity_id'
    //         );
    //         if ($powerunitId) {
    //             User::getCurrentUser()->setLastSelectedPowerunit(
    //                 $this->_powerunitEntity->getId()
    //             )->save();
    //             $this->view->powerunitTitle = $this->_powerunitEntity->getCompanyName();
    //         }
    //         $this->_vendorEntity = new Application_Model_Entity_Entity_Vendor();
    //         $this->_carrierEntity = new Application_Model_Entity_Entity_Carrier();
    //         $this->view->popupSetup = ['vendor_id_title' => $this->_getcarrierVendorPopup()];
    //         $this->_setCarrierVendorCollection();
    //         $this->_helper->layout->disableLayout();
    //     } else {
    //         $this->_helper->redirector('index');
    //     }
    // }



    // private function _getcarrierVendorPopup()
    // {
    //     return [
    //         'gridTitle' => 'Select Vendor',
    //         'destFieldName' => 'vendor_id',
    //         'idField' => 'entity_id',
    //         'collections' => [
    //             'Vendor' => $this->_vendorEntity->getCollection()->getEmptyCollection(),
    //             'Division' => $this->_carrierEntity->getCollection()->getEmptyCollection(),
    //         ],
    //         'callbacks' => [
    //             'name' => Application_Model_Grid_Callback_Text::class,
    //             'tax_id' => Application_Model_Grid_Callback_Text::class,
    //             'contact' => Application_Model_Grid_Callback_Text::class,
    //             'short_code' => Application_Model_Grid_Callback_Text::class,
    //         ],
    //     ];
    // }

    // private function _setDefaultPopup()
    // {
    //     if ($this->_entity->getId()) {
    //         $this->view->popupSetup = [];
    //     } else {
    //         $this->view->popupSetup['powerunit_name']['collections'] = [
    //             $this->getActivePowerunitCollection(),
    //         ];
    //         $this->_setCarrierVendorCollection();
    //     }
    // }

    // private function _setCarrierVendorCollection()
    // {
    //     $this->view->popupSetup['vendor_id_title']['collections'] = [
    //         'Division' => $this->_carrierEntity->getCollection()
    //             ->addConfiguredFilter()
    //             ->addPowerunitStatusFilter()
    //             ->addVisibilityFilterForUser(),
    //         'Vendor' => $this->_vendorEntity->getCollection()
    //             ->addConfiguredFilter()
    //             ->addPowerunitStatusFilter()
    //             ->addVisibilityFilterForUser(),
    //     ];
    // }

    public function getActivePowerunitCollection()
    {
        return $this->_powerunitEntity->getCollection()
            ->addSettlementGroupFilter()
            ->addNonDeletedFilter()
            ->addConfiguredFilter()
            ->addFilterByCarrierContractor();
    }
}
