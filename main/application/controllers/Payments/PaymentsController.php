<?php

use Application_Form_Payments_Payment as PaymentForm;
use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_Payments_Payment as Payment;
use Application_Model_Entity_Payments_Setup as Setup;
use Application_Model_Entity_Settlement_Cycle as Cycle;
use Application_Model_Entity_System_SettlementCycleStatus as CycleStatus;
use Application_Model_Entity_System_SystemValues as SystemValues;

class Payments_PaymentsController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->forward('list');
    }

    public function newAction()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE)) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $setupIdArray = $this->getRequest()->getParam('selectedSetup');
        $powerunitData = $this->getRequest()->getParam('selectedPowerunits');
        $powerunitData = json_decode((string) $powerunitData, true);
        $cycle = User::getCurrentUser()->getCurrentCycle();
        $invoiceDate = $this->_getParam('invoiceDate', false);
        $entity = new Payment();

        foreach ($setupIdArray as $setupId) {
            foreach ($powerunitData as $powerunitId => $contractorId) {
                $setup = Setup::staticLoad([
                    'master_setup_id' => $setupId,
                    'powerunit_id' => $powerunitId,
                    'contractor_id' => $contractorId,
                    'deleted' => SystemValues::NOT_DELETED_STATUS,
                ]);
                if ($setup->getId()) {
                    $entity = new Payment();
                    $entity->setSetupId($setup->getId());
                    $entity->setPowerunitId($powerunitId);
                    $entity->setContractorId($contractorId);
                    $entity->setSettlementCycleId($cycle->getId());
                    $entity->getDefaultValues();
                    if (Zend_Date::isDate($invoiceDate)) {
                        $entity->setInvoiceDate($invoiceDate);
                        $entity->changeDateFormat('invoice_date');
                    }
                    $entity->resetAmount();
                    $entity->resetBalance();
                    $entity->setFromPopup(true);
                    if ($entity->getRecurring()) {
                        $entity->recurring();
                    }
                    $entity->setTaxable($setup->getTaxable());
                    $entity->save();
                    if ($entity->getRecurring()) {
                        $entity->applyRecurring($cycle);
                    }
                }
            }
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->json->sendJson(['entity' => $entity::class]);
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
        $form = new PaymentForm();
        $entity = new Payment();
        $this->view->form = $form;
        $this->view->title = 'Compensation Details';

        if ($id = $this->_getParam('id', 0)) {
            $entity->load($id);
            if (!$entity->checkPermissions()) {
                $this->_helper->redirector('index');
            }
            $cycle = $entity->getSettlementCycle();
            $this->view->cycle = $cycle;
        }

        if ($this->getRequest()->isPost()) {
            if (!$user->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE)) {
                $this->_helper->redirector('index', 'settlement_index');
            }
            $post = $form->configureForm($this->getRequest()->getPost());
            if ($form->isValid($post)) {
                /** @var string[] $values */
                $values = $form->getValues();
                if ($id) {
                    unset($values['balance']);
                    unset($values['amount']);
                    unset($values['source_id']);
                    unset($values['billing_cycle_id']);
                    unset($values['settlement_cycle_id']);
                    unset($values['first_start_day']);
                    unset($values['second_start_day']);
                    unset($values['setup_id']);
                }

                $entity->addData($values);
                if (!$id) {
                    $entity->changeRecurringData(true);
                }
                $entity->changeDateFormat(
                    [
                        'invoice_date',
                        'invoice_due_date',
                        'disbursement_date',
                        'cycle_close_date',
                        'created_datetime',
                        'approved_datetime',
                        'shipment_complete_date',
                    ]
                );

                $entity->save();

                if ($back = $this->getRequest()->getParam('back')) {
                    $this->_redirect($back);
                }
                $this->_helper->redirector('index');
            } else {
                $form->getElement('billing_cycle_id')->setMultiOptions(
                    $entity->getBillingCycleOptions()
                );
                $form->getElement('settlement_cycle_id')->setValue($cycle->getId());
                $post['billing_cycle_id'] = $entity->getBillingCycleId();
                $form->populate($post);
            }
        } else {
            if ($id > 0) {
                $form->getElement('billing_cycle_id')->setMultiOptions(
                    $entity->getBillingCycleOptions()
                );
                $entity->changeRecurringData(true);
                $form->populate(
                    $entity->changeDateFormat(
                        [
                                'invoice_date',
                                'invoice_due_date',
                                'disbursement_date',
                                'cycle_close_date',
                                'created_datetime',
                                'approved_datetime',
                            'shipment_complete_date',
                            ],
                        true
                    )->getData()
                );
                if ($cycle->getStatusId() == CycleStatus::APPROVED_STATUS_ID) {
                    $form->removeElement('submit');
                }
            } else {
                $setupId = $this->_getParam('setup', 0);
                $entity->setSetupId($setupId);
                $form->getElement('billing_cycle_id')->setMultiOptions(
                    $entity->getBillingCycleOptions()
                );
                $entity->changeRecurringData(true);
                $form->populate(
                    $entity->getDefaultValues()->changeDateFormat(
                        [
                                'invoice_date',
                                'invoice_due_date',
                                'disbursement_date',
                                'cycle_close_date',
                                'created_datetime',
                                'approved_datetime',
                            'shipment_complete_date',
                            ],
                        true
                    )->getData()
                );
            }
        }
        $form->configure();
    }

    public function listAction()
    {
        $user = User::getCurrentUser();
        if (!$user->hasPermission(Permissions::SETTLEMENT_DATA_VIEW)) {
            $this->_helper->redirector('index', 'settlement_index');
        }
        $cycle = $user->getCurrentCycle();
        if ($user->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE)) {
            $this->view->setupGrid = new Application_Model_Grid_Payment_PaymentSetup();
            $this->view->powerunitGrid = new Application_Model_Grid_Deduction_Powerunit();
        }
        $this->view->gridModel = new Application_Model_Grid_Payment_Payment();
        $this->view->gridModel->setCyclePeriods($cycle->getAllCyclePeriods());

        $this->view->activeCyclePeriods = $cycle->getCyclePeriods(Cycle::ALL_FILTER_TYPE, Cycle::ONLY_ACTIVE);
        $this->view->cycle = $cycle;
    }

    public function deleteAction()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE)) {
            $this->_helper->redirector('index', 'settlement_index');
        }

        $all = $this->getRequest()->getParam('all', false);
        $entity = Payment::staticLoad((int)$this->getRequest()->getParam('id'));
        $cycle = $entity->getSettlementCycle();

        if ((int)$cycle->getStatusId() !== CycleStatus::APPROVED_STATUS_ID && $entity->checkPermissions()) {
            $entity->delete($all);
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

        $all = $this->getRequest()->getParam('all', false);
        $ids = explode(',', (string) $this->_getParam('ids'));
        $entity = new Payment();

        foreach ($ids as $id) {
            $entity->load((int)$id);
            $cycle = $entity->getSettlementCycle();
            if ((int)$cycle->getStatusId() !== CycleStatus::APPROVED_STATUS_ID && $entity->checkPermissions()) {
                $entity->delete($all);
            }
        }
        if ($back = $this->getRequest()->getParam('back')) {
            $this->_redirect($back);
        }
        $this->_helper->redirector('index');
    }
}
