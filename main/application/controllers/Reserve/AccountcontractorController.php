<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Grid_ReserveAccount_Contractor as ContractorGrid;

class Reserve_AccountcontractorController extends Zend_Controller_Action
{
    /** @var Application_Model_Entity_Accounts_Reserve_Contractor */
    protected $_entity;
    protected $_form;
    protected $_title = 'Contractor Reserve Account';
    protected $_contractorEntity;
    protected $_vendorEntity;
    protected $_carrierEntity;

    public function init()
    {
        $this->_entity = new Application_Model_Entity_Accounts_Reserve_Contractor();
        $this->_form = new Application_Form_Account_Reserve_Contractor();
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
        $this->view->hideContractorSelector = true;
        $user = User::getCurrentUser();
        if ($user->isContractor() || $user->isVendor()) {
            $this->view->form->readonly();
        }
        $raAccountEntity = new Application_Model_Entity_Accounts_Reserve_Vendor();
        $this->_contractorEntity = new Application_Model_Entity_Entity_Contractor();
        $this->_vendorEntity = new Application_Model_Entity_Entity_Vendor();
        $this->_carrierEntity = new Application_Model_Entity_Entity_Carrier();
        $this->view->setupFields = json_encode($raAccountEntity->getResource()->getSetupFields(), JSON_THROW_ON_ERROR);
        $this->view->popupSetup = [
            'contractor_name' => [
                'gridTitle' => 'Select contractor',
                'destFieldName' => 'entity_id',
                'idField' => 'entity_id',
                'collections' => [
                    $this->_contractorEntity->getCollection()->getEmptyCollection(),
                ],
                'callbacks' => [
                    'name' => Application_Model_Grid_Callback_Text::class,
                    'tax_id' => Application_Model_Grid_Callback_Text::class,
                    'contact' => Application_Model_Grid_Callback_Text::class,
                    'short_code' => Application_Model_Grid_Callback_Text::class,
                ],
            ],
            'reserve_account_vendor_id_title' => [
                'gridTitle' => 'Select reserve account',
                'destFieldName' => 'reserve_account_vendor_id',
                'idField' => 'id',
                'collections' => [
                    $raAccountEntity->getCollection()->getEmptyCollection(),
                ],
                'callbacks' => [
                    'priority' => Application_Model_Grid_Callback_Priority::class,
                ],
            ],
            'vendor_id_title' => $this->_getCarrierVendorPopup(),
        ];

        if ($this->getRequest()->isPost()) {
            if (User::getCurrentUser()->isContractor()) {
                $this->_helper->redirector('reporting_index');
            }
            $post = $this->getRequest()->getPost();
            if ($this->_form->isValid($post)) {
                $this->_entity->setData($this->_form->getValues());
                if (!$this->_form->id->getValue()) {
                    $this->_entity->unsPriority();
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
            } else {
                if ($contractorEntityId = $this->_getParam('entity')) {
                    $this->_form->entity_id->setValue($contractorEntityId);
                }
            }
        }
        $this->_setDefaultPopup();
        $this->_form->configure();
        $this->view->showDeleteButton = $this->_entity->isDeletable();
    }

    public function listAction()
    {
        $this->view->gridModel = new ContractorGrid();
    }

    public function deleteAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $raEntity = $this->_entity->load($id)->getReserveAccountEntity();
        if ($this->_entity->checkPermissions()) {
            $raEntity->setDeleted(Application_Model_Entity_System_SystemValues::DELETED_STATUS);
            $raEntity->save();
            $raEntity->deleteHistory();
            $this->_entity->reorderPriority();
            $this->_entity->getResource()->updateReserveAccountVendorInitialBalance($raEntity->getId());
        }
        if ($back = $this->getRequest()->getParam('back')) {
            $this->_redirect($back);
        }
        $this->_helper->redirector('index');
    }

    public function updateracollectionAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $vendorId = $this->_getParam('vendorEntityId');
            $raVendorEntity = new Application_Model_Entity_Accounts_Reserve_Vendor();
            $this->view->popupSetup = [
                'reserve_account_vendor_id_title' => [
                    'gridTitle' => 'Select reserve account',
                    'destFieldName' => 'reserve_account_vendor_id',
                    'idField' => 'id',
                    'collections' => [
                        $raVendorEntity->getCollection()->addFilter('entity_id', $vendorId)->addNonDeletedFilter(),
                    ],
                    'callbacks' => [
                        'priority' => 'Application_Model_Grid_Callback_Priority',
                    ],
                ],
            ];
            $this->_helper->layout->disableLayout();
        } else {
            $this->_helper->redirector('index');
        }
    }

    public function updaterasetupAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $raVendorId = $this->_getParam('raId');
            $reserveAccountEntity = new Application_Model_Entity_Accounts_Reserve_Vendor();
            $data = $reserveAccountEntity->load($raVendorId)->getSetupData();
            $data['min_balance'] = number_format($data['min_balance'], 2);
            $data['contribution_amount'] = number_format($data['contribution_amount'], 2);

            return $this->_helper->json->sendJson($data);
        } else {
            $this->_helper->redirector('index');
        }
    }

    public function updatecarriervendorAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $contractorId = $this->_getParam('contractor-id');
            $this->_contractorEntity = (new Application_Model_Entity_Entity_Contractor())->load(
                $contractorId,
                'entity_id'
            );
            if ($contractorId) {
                User::getCurrentUser()->setLastSelectedContractor(
                    $this->_contractorEntity->getId()
                )->save();
                $this->view->contractorTitle = $this->_contractorEntity->getCompanyName();
            }
            $this->_vendorEntity = new Application_Model_Entity_Entity_Vendor();
            $this->_carrierEntity = new Application_Model_Entity_Entity_Carrier();
            $this->view->popupSetup = ['vendor_id_title' => $this->_getcarrierVendorPopup()];
            $this->_setCarrierVendorCollection();
            $this->_helper->layout->disableLayout();
        } else {
            $this->_helper->redirector('index');
        }
    }

    private function _getcarrierVendorPopup()
    {
        return [
            'gridTitle' => 'Select Vendor',
            'destFieldName' => 'vendor_id',
            'idField' => 'entity_id',
            'collections' => [
                'Vendor' => $this->_vendorEntity->getCollection()->getEmptyCollection(),
                'Division' => $this->_carrierEntity->getCollection()->getEmptyCollection(),
            ],
            'callbacks' => [
                'name' => Application_Model_Grid_Callback_Text::class,
                'tax_id' => Application_Model_Grid_Callback_Text::class,
                'contact' => Application_Model_Grid_Callback_Text::class,
                'short_code' => Application_Model_Grid_Callback_Text::class,
            ],
        ];
    }

    private function _setDefaultPopup()
    {
        if ($this->_entity->getId()) {
            $this->view->popupSetup = [];
        } else {
            $this->view->popupSetup['contractor_name']['collections'] = [
                $this->_contractorEntity->getCollection()->addFilterByActiveCarrierContractor(),
            ];
            $this->_setCarrierVendorCollection();
        }
    }

    private function _setCarrierVendorCollection()
    {
        $this->view->popupSetup['vendor_id_title']['collections'] = [
            'Division' => $this->_carrierEntity->getCollection()->addConfiguredFilter()->addVisibilityFilterForUser()
                ->addContractorStatusFilter(),
            'Vendor' => $this->_vendorEntity->getCollection()->addConfiguredFilter()->addVisibilityFilterForUser()
                ->addContractorStatusFilter(),
        ];
    }
}
