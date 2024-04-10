<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Deductions_Deduction as Deduction;
use Application_Model_Entity_Deductions_Setup as Setup;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_Entity_Type as EntityType;
use Application_Model_Entity_System_SettlementCycleStatus as CycleStatus;
use Application_Model_Entity_System_SystemValues as SystemValues;

class Deductions_DeductionsController extends Zend_Controller_Action
{
    use Application_Plugin_Messager;

    protected $headerErrorMessageTemplate = "The '%s' deduction was not created for the following contractors because %s was not an approved vendor:\n";
    protected $headerMessageTemplate = "The '%s' deduction was not created for the following contractors because %s was not an approved vendor:\n";
    protected $headerWarningMessageTemplate = "The '%s' deduction was created for the following contractors, but the deductions will not be processed because the contractor has rescinded its vendor approval:\n";
    protected $messageTemplate = " - %s\n";
    /**
     * @var  Deduction $_entity
     */
    protected $_entity;

    public function init()
    {
        $this->_entity = new Deduction();
    }

    public function indexAction()
    {
        $this->forward('list');
    }

    public function newAction()
    {
        $user = User::getCurrentUser();
        if (!$user->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE)) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $setupIdArray = $this->getRequest()->getParam('selectedSetup');
        $powerunitData = $this->getRequest()->getParam('selectedPowerunits');
        $powerunitData = json_decode((string) $powerunitData, true);
        $cycleId = $this->getrequest()->getParam('selectedCycle');
        $invoiceDate = $this->_getParam('invoiceDate', false);

        foreach ($setupIdArray as $setupId) {
            foreach ($powerunitData as $powerunitId => $contractorId) {
                $setup = Setup::staticLoad([
                    'master_setup_id' => $setupId,
                    'powerunit_id' => $powerunitId,
                    'contractor_id' => $contractorId,
                    'deleted' => SystemValues::NOT_DELETED_STATUS,
                ]);
                $result = $this->_entity->create($setup, $powerunitId, $contractorId, $cycleId, $invoiceDate);
                if (is_array($result)) {
                    if (isset($result['warning'])) {
                        $this->addWarningMessage(
                            [(new Contractor())->load($contractorId, 'entity_id')->getCompanyName()],
                            'warning-setup-' . $setupId
                        );
                    }
                } elseif ($result === false) {
                    $this->addErrorMessage(
                        [(new Contractor())->load($contractorId, 'entity_id')->getCompanyName()],
                        'error-setup-' . $setupId
                    );
                }
            }

            if ($this->hasErrorMessages('error-setup-' . $setupId)) {
                $setup = Setup::staticLoad($setupId);
                $this->setHeaderMessage(
                    [$setup->getDescription(), $setup->getProvider()->getEntityByType()->getName()],
                    'error-setup-' . $setupId,
                    'error'
                );
            }
            if ($this->hasWarningMessages('warning-setup-' . $setupId)) {
                $setup = Setup::staticLoad($setupId);
                $this->setHeaderMessage(
                    [$setup->getDescription(), $setup->getProvider()->getEntityByType()->getName()],
                    'warning-setup-' . $setupId,
                    'warning'
                );
            }
        }

        // if ($cycleId) {
        //     $this->_entity->reorderImportedPriority($cycleId);
        // }

        if ($this->hasErrorMessages()) {
            $this->_helper->FlashMessenger(
                [
                    'type' => 'T_CHECKBOX_POPUP_ERROR',
                    'title' => 'Deductions were not created.',
                    'messages' => $this->getErrorMessages(),
                    'headerMessages' => $this->getHeaderMessages(),
                ]
            );
        }

        if ($this->hasWarningMessages()) {
            $this->_helper->FlashMessenger(
                [
                    'type' => 'T_CHECKBOX_POPUP_ERROR',
                    'title' => 'Deductions were created that will not be processed.',
                    'messages' => $this->getWarningMessages(),
                    'headerMessages' => $this->getHeaderMessages(),
                ]
            );
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->json->sendJson(['entity' => $this->_entity::class]);
        } else {
            if ($back = $this->getRequest()->getParam('back')) {
                $this->redirect($back);
            }
            $this->_helper->redirector('index');
        }
    }

    public function editAction()
    {
        $user = User::getCurrentUser();
        if (!$user->hasPermission(Permissions::SETTLEMENT_DATA_VIEW)) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $form = new Application_Form_Deductions_Deduction();
        $this->view->form = $form;

        $id = $this->_getParam('id', 0);
        if ($id) {
            $this->_entity->load($id);
            if (!$this->_entity->checkPermissions()) {
                $this->_helper->redirector('index');
            }
            $cycle = $this->_entity->getSettlementCycle();
            $this->view->cycle = $cycle;
            $form->settlement_cycle_id->setValue($cycle->getId());
        }
        $provider = $this->_entity->getProvider();
        if (
            $provider->getEntityTypeId() == EntityType::TYPE_VENDOR && !$user->hasPermission(
                Permissions::VENDOR_DEDUCTION_VIEW
            )
        ) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        if ($this->getRequest()->isPost()) {
            if (
                !$user->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE) || ($user->isOnboarding(
                ) && (!$user->hasPermission(
                    Permissions::VENDOR_DEDUCTION_MANAGE
                ) || !isset($cycle) || $cycle->getStatusId() == CycleStatus::PROCESSED_STATUS_ID))
            ) {
                $this->_helper->redirector('index', 'settlement_index');
            }
            $post = $form->configureForm($this->getRequest()->getPost());

            if (!$id) {
                $maxAmount = str_replace(',', '', (string) $this->_getParam('amount'));
            } else {
                $maxAmount = $this->_entity->getAmount();
            }

            /*$formAmount = str_replace(',', '', (string) $this->_getParam('quantity')) * str_replace(
                ',',
                '',
                (string) $this->_getParam('rate')
            );

            if (!is_null($formAmount) && $formAmount != $maxAmount) {
                $maxAmount = $formAmount;
            }*/

            if ($form->isValid($post)) {
                $values = $form->getValues();
                if ($id) {
                    unset($values['settlement_cycle_id']);
                    unset($values['billing_cycle_id']);
                    unset($values['first_start_day']);
                    unset($values['second_start_day']);
                    unset($values['source_id']);
                    unset($values['setup_id']);
                }
                $this->_entity->addData($values);
                if (!$id) {
                    $this->_entity->changeRecurringData(true);
                }
                if (!$this->_entity->checkCarrierVendorPermissions(true)) {
                    $this->_helper->redirector('index', 'settlement_index');
                }
                $this->_entity->changeDateFormat(
                    [
                        'invoice_date',
                        'invoice_due_date',
                        'disbursement_date',
                        'cycle_close_date',
                        'created_datetime',
                        'approved_datetime',
                    ]
                );
                if (isset($cycle) && $cycle->getStatusId() == CycleStatus::APPROVED_STATUS_ID) {
                    //$this->_entity->save();
                } else {
                    $this->_entity->save();
                }
                if ($back = $this->getRequest()->getParam('back')) {
                    $this->_redirect($back);
                }
                $this->_helper->redirector('index');
            } else {
                $form->getElement('billing_cycle_id')->setMultiOptions(
                    $this->_entity->getBillingCycleOptions()
                );
                if ($id) {
                    $form->settlement_cycle_id->setValue($this->view->cycle->getId());
                }
                $form->populate($post);
            }
        } else {
            if ($id > 0) {
                $form->getElement('billing_cycle_id')->setMultiOptions(
                    $this->_entity->getBillingCycleOptions()
                );
                $this->_entity->changeRecurringData(true);
                $form->populate(
                    $this->_entity->changeDateFormat(
                        [
                            'invoice_date',
                            'invoice_due_date',
                            'disbursement_date',
                            'cycle_close_date',
                            'created_datetime',
                            'approved_datetime',
                        ],
                        true
                    )->getData()
                );
                $this->view->title = 'Edit Deduction #' . $id;
                if ($cycle->getStatusId() == CycleStatus::APPROVED_STATUS_ID) {
                    $form->removeElement('submit');
                    $this->view->title = 'Deduction #' . $id;
                }
            } else {
                $setupId = $this->_getParam('setup', 0);
                $this->_entity->setSetupId($setupId);
                $form->getElement('billing_cycle_id')->setMultiOptions(
                    $this->_entity->getBillingCycleOptions()
                );
                $form->populate(
                    $this->_entity->getDefaultValues()->changeDateFormat(
                        [
                            'invoice_date',
                            'invoice_due_date',
                            'disbursement_date',
                            'cycle_close_date',
                            'created_datetime',
                            'approved_datetime',
                        ],
                        true
                    )->getData()
                );
                $this->view->title = 'Create New Deduction';
            }
        }

        $this->_getPopupSettings($form->provider_id->getValue());
        $form->configure();
        if ($user->isOnboarding()) {
            if (
                !$user->hasPermission(Permissions::VENDOR_DEDUCTION_MANAGE) || $cycle->getStatusId(
                ) > CycleStatus::VERIFIED_STATUS_ID
            ) {
                foreach ($form->getElements() as $element) {
                    $element->setAttrib('readonly', 'readonly');
                }
                $form->removeElement('submit');
            }
        }
    }

    public function listAction()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_DATA_VIEW)) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $cycle = User::getCurrentUser()->getCurrentCycle();
        $this->view->setupGrid = new Application_Model_Grid_Deduction_DeductionSetup();
        $this->view->powerunitGrid = new Application_Model_Grid_Deduction_Powerunit();
        $this->view->gridModel = new Application_Model_Grid_Deduction_Deduction();
        $this->view->gridModel->setCyclePeriods($cycle->getAllCyclePeriods());

        $this->view->activeCyclePeriods = $cycle->getCyclePeriods(
            Application_Model_Entity_Settlement_Cycle::ALL_FILTER_TYPE,
            Application_Model_Entity_Settlement_Cycle::ONLY_ACTIVE
        );
        $this->view->cycle = $cycle;
    }

    public function deleteAction()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE)) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $all = $this->getRequest()->getParam('all', false);
        $this->_entity->load((int) $this->getRequest()->getParam('id'));
        if ($this->_entity->checkPermissions()) {
            $cycle = $this->_entity->getSettlementCycle();
            if (
                $cycle->getStatusId() != CycleStatus::APPROVED_STATUS_ID && (!User::getCurrentUser()->isOnboarding(
                ) || (User::getCurrentUser()->isOnboarding() && User::getCurrentUser()->hasPermission(
                    Permissions::VENDOR_DEDUCTION_MANAGE
                ) && $cycle->getStatusId() < CycleStatus::PROCESSED_STATUS_ID))
            ) {
                $this->_entity->delete($all);
                // $this->_entity->reorderPriority();
            }
        }

        if ($back = $this->getRequest()->getParam('back')) {
            $this->_redirect($back);
        }
        $this->_helper->redirector('index');
    }

    public function multiactionAction()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE)) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $action = $this->_getParam('action-type');
        $ids = explode(',', (string) $this->_getParam('ids'));
        $all = $this->getRequest()->getParam('all', false);
        switch ($action) {
            case 'delete':
                foreach ($ids as $id) {
                    $this->_entity->load((int) $id);
                    if (
                        $this->_entity->getSettlementCycle()->getStatusId(
                        ) != CycleStatus::APPROVED_STATUS_ID && $this->_entity->checkPermissions(
                        ) && (!User::getCurrentUser()->isOnboarding() || (User::getCurrentUser()->isOnboarding(
                        ) && User::getCurrentUser()->hasPermission(
                            Permissions::VENDOR_DEDUCTION_MANAGE
                        ) && $this->_entity->getSettlementCycle()->getStatusId(
                        ) < CycleStatus::PROCESSED_STATUS_ID))
                    ) {
                        $this->_entity->delete($all);
                        // $this->_entity->reorderPriority();
                    }
                }
                break;
        }
        if ($back = $this->getRequest()->getParam('back')) {
            $this->_redirect($back);
        }
        $this->_helper->redirector('index');
    }

    private function _getPopupSettings($entityId)
    {
        $this->view->popupSetup = [
            //            'reserve_account_vendor' => array(
            //                'gridTitle' => 'Select reserve account',
            //                'destFieldName' => 'reserve_account_receiver',
            //                'idField' => 'reserve_account_id',
            //                'collections' => array(
            //                    (new Application_Model_Entity_Accounts_Reserve_Vendor())
            //                        ->getCollection()
            //                        ->addVisibilityFilterForUser()
            //                        ->addFilterByEntity($entityId)
            //                        ->setOrder('priority', Application_Model_Base_Collection::SORT_ORDER_ASC)
            //                ),
            //                'callbacks' => array(
            //                    'priority' => 'Application_Model_Grid_Callback_Priority'
            //                ),
            //                'showClearButton' => false,
            //            ),
        ];
    }

    public function getMessageTemplate()
    {
        return $this->messageTemplate;
    }

    public function getHeaderMessageTemplate($error = false)
    {
        if ($error == 'error') {
            return $this->headerErrorMessageTemplate;
        } elseif ($error == 'warning') {
            return $this->headerWarningMessageTemplate;
        }

        return $this->headerMessageTemplate;
    }
}
