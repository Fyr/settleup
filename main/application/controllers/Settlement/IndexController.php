<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Carrier as Carrier;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_Settlement_Cycle as Cycle;
use Application_Model_Entity_System_SettlementCycleStatus as CycleStatus;
use Application_View_Helper_FlashMessage as FlashMessage;

class Settlement_IndexController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_Entity_Settlement_Cycle
     */
    protected $_entity;

    public function init()
    {
        $this->_entity = new Cycle();
    }

    public function indexAction()
    {
        $this->view->title = 'Settlement';
        $cycleFilterType = $this->getRequest()->getCookie('settlement_cycle_filter_type');
        $cycleFilterYear = $this->getRequest()->getCookie('settlement_cycle_filter_year');
        /** @var Application_Model_Entity_Settlement_Cycle $cycle */
        $user = User::getCurrentUser();
        $cycle = $user->getCurrentCycle();
        $currentSettlementGroupId = $user->getLastSelectedSettlementGroup();
        $this->view->cycle = $cycle;

        $cycleEntity = new Cycle();
        $this->view->cyclePeriods = $periods = $cycleEntity->getAllCyclePeriods();
        $this->view->cycleFilterType = $cycleFilterType;
        $this->view->cycleFilterYear = $cycleFilterYear;

        if ($cycle->getId()) {
            if (!$cycleFilterType) {
                $this->view->cycleFilterType = $cycleFilterType = $cycle->getFilterType();
            } elseif (($cycle->getFilterType(
            ) != $cycleFilterType) && !(isset($periods[1][$cycleFilterYear]) && array_key_exists(
                $cycle->getId(),
                $periods[1][$cycleFilterYear]
            ))) {
                if ($cycleFilterType == Cycle::CURRENT_FILTER_TYPE) {
                    $cycle = $this->view->cycle = $this->_entity->getCollection()->addCarrierFilter()->addSettlementGroupFilter()->getActiveCycle();
                } else {
                    $cycle = $this->view->cycle = $cycleEntity;
                    if (isset($periods[$cycleFilterType])) {
                        $filteredCycles = array_values($periods[$cycleFilterType]);
                        if (isset($filteredCycles[0]) && is_array($filteredCycles[0])) {
                            $filteredCycleIds = array_keys($filteredCycles[0]);
                        } else {
                            $filteredCycleIds = array_keys($periods[$cycleFilterType]);
                        }
                        if (isset($filteredCycleIds[0])) {
                            $cycle = $this->view->cycle = Cycle::staticLoad($filteredCycleIds[0]);
                        }
                    }
                }

                $user->setSettlementCycle($cycle);
            }
        }

        $this->view->paginator = null;
        $this->view->limit = null;
        $this->view->cyclesExists = ((bool)(new Cycle())->getCollection()->addCarrierFilter()->addSettlementGroupFilter()->addNondeletedFilter(
        )->getField('id'));

        if ($cycle && $cycle->getId() && $cycle->getStatusId() != CycleStatus::NOT_VERIFIED_STATUS_ID) {
            $data = $this->updateSessionData();

            $this->view->limit = $data['limit'];
            $this->view->search = $data['search'];
            if (array_key_exists('division', $data['search'])) {
                $this->view->division = $data['search']['division'];
            } else {
                $this->view->division = 0;
            }

            $page = $this->getRequest()->getParam('page', 1);

            $paginator = new Zend_Paginator(
                new Application_Plugin_CycleContractorsPaginatorAdaptor(
                    $cycle,
                    $data['sort'],
                    $data['order'],
                    $data['search']
                )
            );
            $paginator->setItemCountPerPage($data['limit'])->setCurrentPageNumber($page);

            $this->view->paginator = $paginator;

            $this->view->cycleContractors = $cycle->getSettlementContractors(
                $data['sort'],
                $data['order'],
                $data['search']
            );
            $carrierEntity = new Carrier();
            $this->view->carrierCollection = $carrierEntity->getCollection();

            if ($data['limit'] > 0) {
                $totalLimit = $data['limit'];
                $totalOffset = $data['limit'] * ($page - 1);
            } else {
                $totalLimit = null;
                $totalOffset = null;
            }

            $this->view->cycleContractorsTotal = $cycle->getSettlementContractorsTotal(
                $data['search'],
                $totalLimit,
                $totalOffset
            );
        }

        $this->view->showWarningTooltip = $cycle->hasPreviousNotApprovedCycle();
        $this->view->hideContractorSelector = true;

        $this->view->showCreateNewCycleButton = false;
        if ((!$cycle || !$cycle->getId()) && !$this->view->cyclesExists) {
            $this->view->showCreateNewCycleButton = ($user->hasPermission(Permissions::SETTLEMENT_EDIT) && $currentSettlementGroupId);
        }

        if (!$currentSettlementGroupId) {
            $this->_helper->FlashMessenger(
                [
                    'type' => 'T_ERROR',
                    'title' => 'Warning!',
                    'message' => 'You must select a Settlement Group to work with Settlement Cycles.',
                ]
            );
        }

        if ($cycle->getStatusId() == CycleStatus::PROCESSED_STATUS_ID) {
            $message = '';
            foreach ($cycle->getSettlementContractors() as $contractor) {
                if ($contractor['settlement'] < 0) {
                    $message .= '<br/> - ' . $contractor['company'];
                }
            }
            if ($message) {
                $message = 'A settlement amount less than zero cannot be approved for contractors:' . $message;
                $this->_helper->FlashMessenger(
                    [
                        'type' => 'T_WARNING',
                        'title' => 'Warning!',
                        'message' => $message,
                    ]
                );
                $this->view->disableApprove = true;
            }
        }
        if ($cycle->hasNextVerifiedCycle()) {
            $this->view->disableDelete = true;
        }

        if ($reserveAccounts = $cycle->getNegativeReserveAccounts()) {
            $this->view->disableApprove = true;
            $contractor = new Application_Model_Entity_Entity_Contractor();
            $reserveAccount = new Application_Model_Entity_Accounts_Reserve();
            $reserveAccountContractor = new Application_Model_Entity_Accounts_Reserve_Powerunit();
            $message = '';
            foreach ($reserveAccounts as $account) {
                $contractor->load($account->getContractorId(), 'entity_id');
                $reserveAccount->load($account->getReserveAccountId());
                $reserveAccountContractor->load($account->getReserveAccountId(), 'reserve_account_id');
                $message .= 'Withdrawal for ' . $contractor->getCompanyName() . ' (' . $contractor->getCode(
                ) . ') exceeds balance in ' . $reserveAccount->getAccountName(
                ) . ' (' . $reserveAccountContractor->getVendorReserveCode(
                ) . '). ' . 'Correction is required before approval <br>';
            }
            $this->_helper->FlashMessenger(
                [
                    'type' => 'T_ERROR',
                    'title' => 'Warning!',
                    'message' => $message,
                ]
            );
        }

        // show flash messages if there are any
        if ($message = $this->getRequest()->getParam('message')) {
            $message_type = $this->getRequest()->getParam('message_type', FlashMessage::T_INFO);
            $this->_helper->FlashMessenger(
                [
                    'type' => $message_type,
                    'title' => 'Result',
                    'message' => $message,
                ]
            );
        }
    }

    /**
     * update session data
     *
     * @return array
     */
    protected function updateSessionData()
    {
        $storage = Zend_Auth::getInstance()->getStorage()->read();
        $request = $this->getRequest();
        if (!property_exists($storage, 'gridData')) {
            $storage->gridData = [];
        }
        if (!array_key_exists('settlement', $storage->gridData) || $request->getParam('filter') == 'update') {
            $storage->gridData['settlement'] = [];
        }

        $data = $storage->gridData['settlement'];

        $request = $this->getRequest();
        if ($sort = $request->getParam('sort')) {
            $data['sort'] = $sort;
        } else {
            if (!array_key_exists('sort', $data)) {
                $data['sort'] = 'company';
            }
        }

        if ($order = $request->getParam('order')) {
            $data['order'] = $order;
        } else {
            if (!array_key_exists('order', $data)) {
                $data['order'] = 'asc';
            }
        }

        if ($limit = $request->getParam('limit')) {
            $data['limit'] = $limit;
        } else {
            if (!array_key_exists('limit', $data)) {
                $data['limit'] = '25';
            }
        }

        $searchParams = [
            'code',
            'division',
            'settlement_group',
            'company',
            'payments',
            'deductions_amount',
            'deductions_balance',
            'contributions',
            'withdrawal',
            'settlement',
        ];

        $search = array_intersect_key($request->getParams(), array_flip($searchParams));
        if (!array_key_exists('search', $data)) {
            $data['search'] = [];
        }

        $data['search'] = array_merge($data['search'], $search);

        $storage->gridData['settlement'] = $data;

        return $data;
    }

    public function newAction()
    {
        $user = User::getCurrentUser();
        if ($user->hasPermission(Permissions::SETTLEMENT_EDIT)) {
            if (!$user->getLastSelectedSettlementGroup()) {
                $this->_helper->redirector('index');
            }
            $rule = (new Carrier())->getCycleRule();
            if ($rule->getId()) {
                $this->forward('edit');
            } else {
                if (!$user->hasPermission(Permissions::SETTLEMENT_RULE_MANAGE)) {
                    $this->_helper->redirector('index');
                }
                $this->_helper->redirector('edit', 'settlement_rule');
            }
        } else {
            $this->_helper->redirector('index');
        }
    }

    public function editAction()
    {
        $form = new Application_Form_Settlement_Cycle();
        $id = $this->_getParam('id');

        if ($id) {
            $this->_entity->load($id);
        }

        $form->configureCloseDate($id);

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();

            if ($post['cycle_period_id'] == Application_Model_Entity_System_CyclePeriod::SEMY_MONTHLY_PERIOD_ID) {
                $form->first_start_day->setRequired(true);
                $form->second_start_day->setRequired(true);
            }

            if ($form->isValid($post)) {
                if ($post['cycle_period_id'] == Application_Model_Entity_System_CyclePeriod::SEMI_WEEKLY_PERIOD_ID) {
                    $form->first_start_day->setValue($form->week_day->getValue());
                    $form->second_start_day->setValue($form->second_week_day->getValue());
                }
                $values = $form->getValues();
                if ($values['id']) {
                    unset($values['cycle_period_id']);
                }
                $this->_entity->addData($values);
                if ($form->cycle_close_date instanceof Zend_Form_Element_Select) {
                    $this->_entity->setIsCustomCloseDate(true);
                }
                $this->_entity->changeDateFormat([
                    'cycle_start_date',
                    'cycle_close_date',
                    'processing_date',
                    'disbursement_date',
                ]);
                $this->_entity->setSettlementGroupId(
                    User::getCurrentUser()->getLastSelectedSettlementGroup()
                );
                //                $this->_entity->changeRecurringData();
                $this->_entity->save();
                //                $this->_entity->updateProcessingDate();
                //                $this->_entity->updateDisbursementDate();
                //                $this->_entity->save();

                $this->_helper->redirector('index');
            } else {
                $form->getElement('cycle_period_id')->setMultiOptions(
                    $this->_entity->getBillingCycleOptions()
                );
                $form->populate($post);
                if ($form->cycle_period_id->getValue(
                ) == Application_Model_Entity_System_CyclePeriod::SEMI_WEEKLY_PERIOD_ID) {
                    $form->week_day->setValue($form->first_start_day->getValue());
                    $form->second_week_day->setValue($form->second_start_day->getValue());
                }
            }
        } else {
            $form->getElement('cycle_period_id')->setMultiOptions(
                $this->_entity->getBillingCycleOptions()
            );
            if ($id) {
                $form->getElement('cycle_period_id')->setMultiOptions(
                    $this->_entity->getBillingCycleOptions()
                );
                $form->populate(
                    $this->_entity->load($id)->changeDateFormat([
                            'cycle_start_date',
                            'cycle_close_date',
                            'processing_date',
                            'disbursement_date',
                        ], true)
                        //                        ->changeRecurringData(true)
                        ->getData()
                );
                if ($form->cycle_period_id->getValue(
                ) == Application_Model_Entity_System_CyclePeriod::SEMI_WEEKLY_PERIOD_ID) {
                    $form->week_day->setValue($form->first_start_day->getValue());
                    $form->second_week_day->setValue($form->second_start_day->getValue());
                }
                if (!$this->_entity->checkPermissions()) {
                    $this->_helper->redirector('index');
                }
                $form->getElement('cycle_status')->setValue($this->_entity->getStatus()->getTitle());
                if (!$this->_entity->isFirstCycle()) {
                    //                    $form->getElement('cycle_start_date')
                    //                         ->setAttrib('readonly', true);

                    $form->getElement('cycle_period_id')->setAttrib('readonly', true);

                    $form->getElement('cycle_period_id')->setMultiOptions(
                        [
                            $form->getElement('cycle_period_id')->getValue() => $form->getElement(
                                'cycle_period_id'
                            )->getMultiOption(
                                $form->getElement('cycle_period_id')->getValue()
                            ),
                        ]
                    );
                }
            } else {
                if (!$this->_entity->checkPermissions(true)) {
                    $this->_helper->redirector('index');
                }
                $rule = (new Carrier())->getCycleRule();
                if (!$rule->getId()) {
                    $this->_helper->redirector('edit', 'settlement_rule');
                } else {
                    $rule->unsetData('id');
                    $cycle = (new Cycle())->setData($rule->getData());
                    $closeDate = (new Application_Model_Entity_System_CyclePeriod())->setId(
                        $cycle->getCyclePeriodId()
                    )->getPeriodLength($cycle);
                    $cycle->setCycleCloseDate($closeDate);
                    $cycle->setProcessingDate(
                        (new Zend_Date($closeDate, Zend_Date::ISO_8601))->addDay($cycle->getPaymentTerms())->toString(
                            'MM/dd/yyyy'
                        )
                    );
                    $cycle->setDisbursementDate(
                        (new Zend_Date($closeDate, Zend_Date::ISO_8601))->addDay(
                            $cycle->getDisbursementTerms()
                        )->toString('MM/dd/yyyy')
                    );
                    $cycle->changeDateFormat(
                        ['cycle_start_date', 'cycle_close_date'],
                        true
                    );
                    //                    $cycle->changeRecurringData(true);

                    $form->populate($cycle->getData());
                    if ($form->cycle_period_id->getValue(
                    ) == Application_Model_Entity_System_CyclePeriod::SEMI_WEEKLY_PERIOD_ID) {
                        $form->week_day->setValue($form->first_start_day->getValue());
                        $form->second_week_day->setValue($form->second_start_day->getValue());
                    }
                }
            }
        }

        $this->view->title = ($id) ? 'Edit Settlement Cycle ' : 'Create New Settlement Cycle';

        $this->view->form = $form;
        $form->configure();
    }

    //    public function listAction()
    //    {
    //        $this->view->title = 'Settlement Cycles';
    //        $this->view->entity = $this->_entity;
    //    }

    public function contractorAction()
    {
        $user = User::getCurrentUser();
        $contractorId = $this->_getParam('id');
        $cycle = $user->getCurrentCycle();
        $this->view->cycle = $cycle;
        $this->view->contractor = current($cycle->getSettlementContractors('id', 'asc', null, $contractorId));

        $contractor = (new Application_Model_Entity_Entity_Contractor())->load($contractorId, 'entity_id');
        if ($contractor->getId()) {
            $user->setLastSelectedContractor($contractor->getId())->save();
            $this->view->contractorEntity = $contractor;
        }

        $hasViewPermission = $user->hasPermission(Permissions::SETTLEMENT_DATA_VIEW);
        if ($hasViewPermission) {
            $this->view->paymentGrid = new Application_Model_Grid_Settlement_Payment();
            $this->view->deductionGrid = new Application_Model_Grid_Settlement_Deduction();
            $this->view->transactionGrid = new Application_Model_Grid_Settlement_Transaction();
        }
        $this->view->accountsGrid = new Application_Model_Grid_Settlement_AccountHistory();

        $hasEditPermission = $user->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE);
        if ($hasEditPermission) {
            $this->view->deductionSetupGrid = new Application_Model_Grid_Settlement_DeductionSetup();
            $this->view->paymentSetupGrid = new Application_Model_Grid_Settlement_PaymentSetup();
            $this->view->contributionGrid = new Application_Model_Grid_Settlement_Contribution();
            $this->view->withdrawalGrid = new Application_Model_Grid_Settlement_Withdrawal();
        }

        $this->view->title = 'Contractor Settlement';
        $this->view->isStatusVerified = $cycle->getStatusId() == CycleStatus::VERIFIED_STATUS_ID;
        $this->view->user = $user;
        $this->view->isStatementEnabled = ($cycle->getStatusId() == CycleStatus::VERIFIED_STATUS_ID
            || !$user->hasPermission(Permissions::REPORTING_GENERAL));
        $this->view->hasViewPermission = $hasViewPermission;
        $this->view->hasEditPermission = $hasEditPermission;

        $currYear = date('Y');
        $this->view->dataYTD = $cycle->getSettlementPowerunitTotalsByPeriod("$currYear-01-01", "$currYear-12-31");
    }

    public function processAction()
    {
        if (!$this->_entity->checkPermissions(true)) {
            $this->_helper->redirector('index');
        }
        $this->_process('process');
    }

    public function verifyAction()
    {
        if (!$this->_entity->checkPermissions(true)) {
            return $this->_helper->redirector('index');
        }
        $this->_process('verify');
    }

    public function approveAction()
    {
        if (!$this->_entity->checkPermissions(true)) {
            return $this->_helper->redirector('index');
        }
        $this->_process('approve');
    }

    public function rejectAction()
    {
        if (!$this->_entity->checkPermissions(true)) {
            return $this->_helper->redirector('index');
        }
        $this->_process('reject');
    }

    public function deleteAction()
    {
        if (!$this->_entity->checkPermissions(true)) {
            $this->_helper->redirector('index');
        }
        $this->_process('clear');
    }

    /**
     * Export settlement data to Hub
     *
     * @return void
     * @throws Exception
     */
    public function exportAction(): void
    {
        // verify permissions
        if (!$this->_entity->checkPermissions(true)) {
            $this->_helper->redirector('index');
        }

        $this->_process('export_to_hub');
    }

    protected function _process($processName)
    {
        $result = null;
        if ($cycleId = $this->_getParam('id')) {
            $this->_entity->load($cycleId);

            try {
                $result = $this->_entity->$processName();
                if (!$cookieCycleId = $this->getRequest()->getCookie('settlement_cycle_id')) {
                    setcookie('settlement_cycle_id', (string) $cycleId, ['expires' => time() + 3600, 'path' => '/']);
                }
                if ($processName == 'approve') {
                    setcookie('settlement_cycle_filter_type', Cycle::LAST_CLOSED_FILTER_TYPE, ['expires' => time() + 3600, 'path' => '/']);

                    return $this->_helper->redirector('index');
                }
            } catch (Exception $e) {
                if ($e->getMessage() == Cycle::CYCLE_STAGE_ERROR) {
                    return $this->_helper->redirector('index');
                } else {
                    throw $e;
                }
            }
        }

        // pass result to a flash messenger if it's not empty
        if ($result) {
            if ($result instanceof FlashMessage) {
                $params = ['message' => $result->getMessage(), 'message_type' => $result->getType()];
            } else {
                $params = ['message' => $result];
            }
            $this->_helper->redirector(
                'index',
                $this->getRequest()->getControllerName(),
                $this->getRequest()->getModuleName(),
                $params
            );
        }

        return $this->_helper->redirector('index');
    }
}
