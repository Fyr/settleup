<?php

class GridTest extends BaseTestCase
{
    protected static $_grid;
    protected $view;

    public function testModel()
    {
        Application_Model_Entity_Accounts_User::login(16);
        $storage = Zend_Auth::getInstance()
            ->getStorage()
            ->read();
        $storage->currentControllerName = 'carrier_index';
        $storage->isNotGridRequest = 'true';

        $data['header'] = [
            'header' => [
                'id' => '#',
                'name_on_account' => 'Company',
            ],
            'sort' => [
                'id' => 'ASC',
                'name_on_account' => 'DESC',
            ],
            'filter' => 'true',
            'service' => [
                'header' => [
                    'action' => 'Action',
                ],
                'bindOn' => 'id',
                'action' => [
                    'edit' => [
                        'url' => '#',
                        'style' => [
                            'button' => 'btn-primary',
                            'icon_style' => 'icon-pencil',
                        ],
                    ],
                    'delete' => [
                        'url' => '#',
                        'confirm-type' => 'delete',
                        'style' => [
                            'button' => 'btn-danger confirm',
                            'icon_style' => 'icon-trash',
                        ],
                    ],
                ],
            ],
        ];
        $data['filter'] = [
            'name_on_account' => 'a',
        ];
        $data['limit'] = '25';
        $data['massaction'] = [
            'delete' => [
                'caption' => 'Delete Selected',
                'button_class' => 'btn-danger confirm-delete btn-multiaction',
                'confirm-type' => 'delete',
                'icon_class' => 'icon-trash',
                'style' => 'display:none',
                'action-type' => 'delete',
                'url' => '#',
            ],
        ];

        $data['customFilters'] = null;
        $data['currentPage'] = '1';
        $data['buttons'] = null;
        self::$_grid = new Application_Model_Grid(
            $data['entity'],
            $data['header'],
            $data['massaction'],
            $data['customFilters'],
            $data['buttons'],
            $data['filter'],
            $data['limit'],
            $data['currentPage']
        );

        return $data;
    }

    public function testGetDebugString()
    {
        self::$_grid->getDebugString();
    }

    public function testGetLimitArray()
    {
        self::$_grid->getLimitArray();
    }

    /**
     * @depends testModel
     */
    public function testGetPagerForEntity(array $data)
    {
        Application_Model_Entity_Accounts_User::login(16);
        $storage = Zend_Auth::getInstance()
            ->getStorage()
            ->read();
        $storage->currentControllerName = 'carrier_index';
        $storage->isNotGridRequest = 'true';
        $data['header']['header'] = [
            'id' => '#',
            'balance' => 'Balance',
        ];
        $data['header']['sort'] = [
            'id' => 'DESC',
            'balance' => 'ASC',
        ];
        $data['filter'] = [
            'description' => 'a',
        ];
        $data['entity'] = 'Application_Model_Entity_Deductions_Deduction';
        $data['filter']['settlement_cycle_id_filter'] = '1';
        $data['filter']['settlement_cycle_filter_year'] = '2014';
        $data['filter']['value'] = [''];
        $grid = new Application_Model_Grid(
            $data['entity'],
            $data['header'],
            $data['massaction'],
            $data['customFilters'],
            $data['buttons'],
            $data['filter'],
            $data['limit'],
            $data['currentPage']
        );
        $grid->getPagerForEntity();
        return $data;
    }

    /**
     * @depends testGetPagerForEntity
     */
    public function testGetPagerForEntityFilter2(array $data)
    {
        Application_Model_Entity_Accounts_User::login(16);
        $storage = Zend_Auth::getInstance()
            ->getStorage()
            ->read();
        $storage->currentControllerName = 'carrier_index';
        $storage->isNotGridRequest = 'true';
        $data['filter'] = ['priority' => '1'];
        $grid = new Application_Model_Grid(
            $data['entity'],
            $data['header'],
            $data['massaction'],
            $data['customFilters'],
            $data['buttons'],
            $data['filter'],
            $data['limit'],
            $data['currentPage']
        );
        $grid->getPagerForEntity();
        return $data;
    }

    /**
     * @depends testGetPagerForEntityFilter2
     */
    public function testGetPagerForEntityFilter3(array $data)
    {
        Application_Model_Entity_Accounts_User::login(16);
        $storage = Zend_Auth::getInstance()
            ->getStorage()
            ->read();
        $storage->currentControllerName = 'carrier_index';
        $storage->isNotGridRequest = 'true';
        $data['entity'] = 'Application_Model_Entity_Settlement_Cycle';
        $data['filter'] = ['settlement_cycle_id' => '1'];

        $data['header']['header'] = [
            'id' => '#',
            'cycle_period_id' => 'Id',
        ];
        $data['header']['sort'] = [
            'id' => 'DESC',
            'cycle_period_id' => 'ASC',
        ];
        $grid = new Application_Model_Grid(
            $data['entity'],
            $data['header'],
            $data['massaction'],
            $data['customFilters'],
            $data['buttons'],
            $data['filter'],
            $data['limit'],
            $data['currentPage']
        );
        $grid->getPagerForEntity();
    }

    /**
     * @depends testModel
     */
    //    public function testSetPriority(array $data)
    //    {
    //        Application_Model_Entity_Accounts_User::login(16);
    //        $storage= Zend_Auth::getInstance()->getStorage()->read();
    //        $storage->currentControllerName ='carrier_index';
    //        $storage->isNotGridRequest ='true';
    //        $data['current_page'] = '1';
    //        $data['customFilters'] = array(array(
    //            'name'=>'addNonDeletedFilter',
    //            'value'=>''
    //        )
    //        );
    //        $grid = new Application_Model_Grid(
    //            $data['entity'],
    //            $data['header'],
    //            $data['massaction'],
    //            $data['customFilters'],
    //            $data['buttons'],
    //            $data['filter'],
    //            $data['limit'],
    //            $data['currentPage']
    //        );
    //        $grid->setPriority(
    //            array('3','3'),
    //            array('3','2'),
    //            1
    //        );
    //    }

    /**
     * @depends testModel
     */
    public function testSetQuckEdit(array $data)
    {
        Application_Model_Entity_Accounts_User::login(16);
        $storage = Zend_Auth::getInstance()
            ->getStorage()
            ->read();
        $storage->currentControllerName = 'carrier_index';
        $storage->isNotGridRequest = 'true';
        $data['header']['quickEdit'] = ['EXCEPT'];
        $grid = new Application_Model_Grid(
            $data['entity'],
            $data['header'],
            $data['massaction'],
            $data['customFilters'],
            $data['buttons'],
            $data['filter'],
            $data['limit'],
            $data['currentPage']
        );
    }

    /**
     * @depends testModel
     */
    public function testGetPropertyData(array $data)
    {
        Application_Model_Entity_Accounts_User::login(16);
        $storage = Zend_Auth::getInstance()
            ->getStorage()
            ->read();
        $storage->currentControllerName = 'carrier_index';
        $storage->isNotGridRequest = 'true';
        $grid = new Application_Model_Grid(
            $data['entity'],
            $data['header'],
            $data['massaction'],
            $data['customFilters'],
            $data['buttons'],
            $data['filter'],
            $data['limit'],
            $data['currentPage']
        );
        $grid->controllerStorage = ['null' => ''];
        $grid->getPropertyData('null');
        $grid->getPropertyData(null);
    }

    /**
     * @depends testModel
     */
    public function testGetSortData(array $data)
    {
        Application_Model_Entity_Accounts_User::login(16);
        $storage = Zend_Auth::getInstance()
            ->getStorage()
            ->read();
        $storage->currentControllerName = 'carrier_index';
        $storage->isNotGridRequest = 'true';
        unset($data['header']['sort']);
        $grid = new Application_Model_Grid(
            $data['entity'],
            $data['header'],
            $data['massaction'],
            $data['customFilters'],
            $data['buttons'],
            $data['filter'],
            $data['limit'],
            $data['currentPage']
        );
        $grid->controllerStorage = ['null' => ''];
        $grid->getSortData();
    }

    /**
     * @depends testModel
     */
    public function testSetQuckEdit2(array $data)
    {
        Application_Model_Entity_Accounts_User::login(16);
        $storage = Zend_Auth::getInstance()
            ->getStorage()
            ->read();
        $storage->currentControllerName = 'carrier_index';
        $storage->isNotGridRequest = 'true';
        $data['header']['quickEdit'] = [];
        $grid = new Application_Model_Grid(
            $data['entity'],
            $data['header'],
            $data['massaction'],
            $data['customFilters'],
            $data['buttons'],
            $data['filter'],
            $data['limit'],
            $data['currentPage']
        );
    }

    /**
     * @depends testModel
     */
    public function testSetQuckEdit3(array $data)
    {
        Application_Model_Entity_Accounts_User::login(16);
        $storage = Zend_Auth::getInstance()
            ->getStorage()
            ->read();
        $storage->currentControllerName = 'carrier_index';
        $storage->isNotGridRequest = 'true';
        $data['header']['quickEdit'] = ['EXCEPT1'];
        $grid = new Application_Model_Grid(
            $data['entity'],
            $data['header'],
            $data['massaction'],
            $data['customFilters'],
            $data['buttons'],
            $data['filter'],
            $data['limit'],
            $data['currentPage']
        );
    }

    public function testGetFilter()
    {
        self::$_grid->getFilter('name_on_account');
    }

    /**
     * @depends testModel
     */
    public function testHideButtons(array $data)
    {
        Application_Model_Entity_Accounts_User::login(16);
        $storage = Zend_Auth::getInstance()
            ->getStorage()
            ->read();
        $storage->currentControllerName = 'carrier_index';
        $storage->isNotGridRequest = 'true';
        $data['filter'] = [];
        $data['header']['header'] = [
            'id' => '#',
        ];
        $data['header']['sort'] = [
            'id' => 'DESC',
        ];
        $data['entity'] = 'Application_Model_Entity_Accounts_Reserve_Transaction';
        $grid = new Application_Model_Grid(
            $data['entity'],
            $data['header'],
            $data['massaction'],
            $data['customFilters'],
            $data['buttons'],
            $data['filter'],
            $data['limit'],
            $data['currentPage']
        );
        $grid->hideButtons();
    }

    //callbacks
    public function startTest($model)
    {
        $model->body();
        $model->wrapper();
    }

    public function testGridAction()
    {
        $model = Application_Model_Grid_Callback_Action::getInstance();
        $this->startTest($model);
        $model->render(
            [
                'id' => '',
                'settlement_cycle_status' => Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID,
            ],
            '',
            ''
        );
        $this->startTest($model);
    }

    public function testGridActionContractors()
    {
        $model = Application_Model_Grid_Callback_ActionContractors::getInstance();
        $this->startTest($model);
        $model->render(
            [
                'id' => '',
                'status' => Application_Model_Entity_System_ContractorStatus::STATUS_NOT_CONFIGURED,
            ],
            '',
            ''
        );
        $this->startTest($model);
        $model->render(
            [
                'id' => '',
                'status' => Application_Model_Entity_System_ContractorStatus::STATUS_TERMINATED,
            ],
            '',
            ''
        );
        $this->startTest($model);
    }

    public function testGridActionDeductions()
    {
        $model = Application_Model_Grid_Callback_ActionDeductions::getInstance();
        $this->startTest($model);
        $model->render(
            [
                'id' => '',
                'settlement_cycle_status' => Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID,
                'provider_entity_type_id' => Application_Model_Entity_Entity_Type::TYPE_VENDOR,
            ],
            '',
            ''
        );
        $this->startTest($model);
    }

    public function testGridActionDisbursement()
    {
        $model = Application_Model_Grid_Callback_ActionDisbursement::getInstance();
        $this->startTest($model);
        $model->render(
            [
                'id' => '',
                'disbursement_status' => Application_Model_Entity_System_PaymentStatus::APPROVED_STATUS,
            ],
            '',
            ''
        );
        $this->startTest($model);
    }

    public function testGridActionPayments()
    {
        $model = Application_Model_Grid_Callback_ActionPayments::getInstance();
        $this->startTest($model);
        $model->render(
            [
                'id' => '',
                'settlement_cycle_status' => Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID,
            ],
            '',
            ''
        );
        $this->startTest($model);
    }

    //    /**
    //     * @depends testModel
    //     */
    //    public function testGridActionReserveAccountHistory(array $data)
    //    {
    //        Application_Model_Entity_Accounts_User::login(16);
    //        $storage= Zend_Auth::getInstance()->getStorage()->read();
    //        $storage->currentControllerName ='carrier_index';
    //        $storage->isNotGridRequest ='true';
    //
    //        $model = Application_Model_Grid_Callback_ActionReserveAccountHistory::getInstance();
    //        $view = $this->view;
    //        $this->view{'gridModel'} = new Application_Model_Grid(
    //            $data['entity'],
    //            $data['header'],
    //            $data['massaction'],
    //            $data['customFilters'],
    //            $data['buttons'],
    //            $data['filter'],
    //            $data['limit'],
    //            $data['currentPage']
    //        );
    //        $model->render('','',$view);
    //
    //    }

    public function testGridActionReserveAccounts()
    {
        $model = Application_Model_Grid_Callback_ActionReserveAccounts::getInstance();
        $this->startTest($model);
        $model->render(
            [
                'id' => '',
                'settlement_cycle_status' => Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID,
            ],
            '',
            ''
        );
        $this->startTest($model);
    }

    public function testGridActionReserveTransaction()
    {
        $model = Application_Model_Grid_Callback_ActionReserveTransaction::getInstance();
        $this->startTest($model);
        $model->render(
            [
                'id' => '',
                'settlement_cycle_status' => Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID,
            ],
            '',
            ''
        );
        $this->startTest($model);
    }

    public function testGridActionSettlementDeductions()
    {
        $model = Application_Model_Grid_Callback_ActionSettlementDeductions::getInstance();
        $this->startTest($model);
        $model->render(
            [
                'id' => '',
                'settlement_cycle_status' => Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID,
            ],
            '',
            ''
        );
        $this->startTest($model);
    }

    public function testGridActionSettlementPayments()
    {
        $model = Application_Model_Grid_Callback_ActionSettlementPayments::getInstance();
        $this->startTest($model);
        $model->render(
            [
                'id' => '',
                'settlement_cycle_status' => Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID,
            ],
            '',
            ''
        );
        $this->startTest($model);
    }

    public function testGridActionSettlementReserveTransaction()
    {
        $model = Application_Model_Grid_Callback_ActionSettlementReserveTransaction::getInstance();
        $this->startTest($model);
        $model->render(
            [
                'id' => '',
                'settlement_cycle_status' => Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID,
            ],
            '',
            ''
        );
        $this->startTest($model);
    }

    public function testGridAmount()
    {
        $model = Application_Model_Grid_Callback_Amount::getInstance();
        $model->body();
        $model->wrapper();
    }

    public function testGridBalance()
    {
        $model = Application_Model_Grid_Callback_Balance::getInstance();
        $model->body();
        $model->wrapper();
    }

    public function testGridBankAccountAmountLimitType()
    {
    }

    public function testGridBankAccountNumber()
    {
        $this->loginUser();
        $model = Application_Model_Grid_Callback_BankAccountNumber::getInstance();
        $this->startTest($model);
        $model->render(
            ['id' => ''],
            '123456789',
            ''
        );
        $this->startTest($model);
    }

    public function testGridBankAccountType()
    {
        $this->loginUser();
        $model = Application_Model_Grid_Callback_BankAccountType::getInstance();
        $this->startTest($model);
        $model->render(
            [
                'id' => '',
                'payment_type' => Application_Model_Entity_System_PaymentType::CONST_PAYMENT_TYPE_CHECK,
            ],
            '',
            ''
        );
        $this->startTest($model);
    }

    public function testGridContractorReserveCode()
    {
        $model = Application_Model_Grid_Callback_ContractorReserveCode::getInstance();
        $model->body();
        $model->wrapper();
    }

    public function testGridCreatedByCarrier()
    {
        $model = Application_Model_Grid_Callback_CreatedBy::getInstance();
        $model->render(
            [
                'id' => '',
                'name' => 'name',
                'carrier_tax_id' => 'cti',
            ],
            '1',
            ''
        );
        $model->body();
        $model->wrapper();
    }

    public function testGridCreatedByContractor()
    {
        $model = Application_Model_Grid_Callback_CreatedBy::getInstance();
        $model->render(
            [
                'id' => '',
                'name' => null,
                'contractor_tax_id' => 'cti',
            ],
            '1',
            ''
        );
        $model->body();
        $model->wrapper();
    }

    public function testGridDeductionBalance()
    {
        $model = Application_Model_Grid_Callback_DeductionBalance::getInstance();
        $model->render(
            ['id' => ''],
            '1',
            ''
        );
        $this->startTest($model);
    }

    public function testGridDeductionBalanceNoCol()
    {
        $model = Application_Model_Grid_Callback_DeductionBalance::getInstance();
        $model->render(
            ['id' => ''],
            '',
            ''
        );
        $this->startTest($model);
    }

    public function testGridDisbursementId()
    {
        $model = Application_Model_Grid_Callback_DisbursementId::getInstance();
        $model->render(
            [
                'tax_id' => '1',
                'carrier_tax_id' => '2',
            ],
            '',
            ''
        );
        $model->body();
        $model->wrapper();
    }

    public function testGridDisbursementIdNoTaxId()
    {
        $model = Application_Model_Grid_Callback_DisbursementId::getInstance();
        $model->render(
            [
                'tax_id' => false,
                'contractor_tax_id' => '3',
            ],
            '',
            ''
        );
        $model->body();
        $model->wrapper();
    }

    public function testGridFrequency()
    {
        $model = Application_Model_Grid_Callback_Frequency::getInstance();
        $this->startTest($model);
        $model->render(
            [
                'id' => '',
                'recurring' => 'true',
            ],
            '1',
            ''
        );
        $this->startTest($model);
    }

    public function testGridNum()
    {
        $model = Application_Model_Grid_Callback_Num::getInstance();
        $model->body();
        $model->wrapper();
    }

    public function testGridPercentage()
    {
        $model = Application_Model_Grid_Callback_Percentage::getInstance();
        $this->startTest($model);
        $model->render(
            [
                'id' => '',
                'limit_type' => 0,
            ],
            '1',
            ''
        );
        $this->startTest($model);
    }

    public function testGridPriority()
    {
        $model = Application_Model_Grid_Callback_Priority::getInstance();
        $model->body();
        $model->wrapper();
    }

    public function testGridQuantity()
    {
        $model = Application_Model_Grid_Callback_Quantity::getInstance();
        $model->body();
        $model->wrapper();
    }

    public function testGridQuickEditAdjustedBalance()
    {
        $model = Application_Model_Grid_Callback_QuickEditAdjustedBalance::getInstance();
        $this->startTest($model);
        $model->render(
            [
                'id' => '',
                'amount' => '100',
                'settlement_cycle_status' => Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID,
            ],
            1,
            ''
        );
        $this->startTest($model);
    }

    public function testGridQuickEditAmount()
    {
        $model = Application_Model_Grid_Callback_QuickEditAmount::getInstance();
        $this->startTest($model);
        $model->render(
            [
                'id' => '',
                'settlement_cycle_status' => Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID,
                'type' => Application_Model_Entity_System_ReserveTransactionTypes::ADJUSTMENT_DECREASE,
            ],
            1,
            ''
        );
        $this->startTest($model);
    }

    public function testGridQuickEditAmountLess()
    {
        $model = Application_Model_Grid_Callback_QuickEditAmount::getInstance();
        $this->startTest($model);
        $model->render(
            [
                'id' => '',
                'settlement_cycle_status' => '8',
                'type' => Application_Model_Entity_System_ReserveTransactionTypes::ADJUSTMENT_DECREASE,
            ],
            1,
            ''
        );
        $this->startTest($model);
    }

    public function testGridQuickEditQuantity()
    {
        $model = Application_Model_Grid_Callback_QuickEditQuantity::getInstance();
        $this->startTest($model);
        $model->render(
            [
                'id' => '',
                'settlement_cycle_status' => '20',
            ],
            '',
            ''
        );
        $this->startTest($model);
    }

    public function testGridQuickEditRate()
    {
        $model = Application_Model_Grid_Callback_QuickEditRate::getInstance();
        $this->startTest($model);
        $model->render(
            [
                'id' => '',
                'settlement_cycle_status' => '20',
            ],
            1,
            ''
        );
        $this->startTest($model);
    }

    public function testGridSettlementCurrentBalance()
    {
        $model = Application_Model_Grid_Callback_SettlementCurrentBalance::getInstance();
        $model->body();
        $model->wrapper();
    }

    public function testGridSettlementQuickEditAmount()
    {
        $model = Application_Model_Grid_Callback_SettlementQuickEditAmount::getInstance();
        $model->body();
        $model->wrapper();
    }

    public function testGridSettlementQuickEditAmountSettlementStatus()
    {
        $model = Application_Model_Grid_Callback_SettlementQuickEditAmount::getInstance();
        $model->render(
            [
                'id' => '',
                'settlement_cycle_status' => Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID,
                'type' => Application_Model_Entity_System_ReserveTransactionTypes::ADJUSTMENT_DECREASE,
            ],
            1,
            ''
        );
        $model->body();
        $model->wrapper();
    }

    public function testGridSettlementQuickEditAmountLess()
    {
        $model = Application_Model_Grid_Callback_SettlementQuickEditAmount::getInstance();
        $model->render(
            [
                'id' => '',
                'settlement_cycle_status' => '8',
            ],
            1,
            ''
        );
        $model->body();
        $model->wrapper();
    }

    public function testGridSettlementTransactionCheckbox()
    {
        $model = Application_Model_Grid_Callback_SettlementTransactionCheckbox::getInstance();
        $model->body();
        $model->wrapper();
    }

    public function testGridSettlementTransactionCheckboxSettlementStatus()
    {
        $model = Application_Model_Grid_Callback_SettlementTransactionCheckbox::getInstance();
        $model->render(
            [
                'id' => '',
                'settlement_cycle_status' => Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID,
                'type' => Application_Model_Entity_System_ReserveTransactionTypes::ADJUSTMENT_DECREASE,
            ],
            1,
            ''
        );
        $model->body();
        $model->wrapper();
    }

    public function testGridText()
    {
        $model = Application_Model_Grid_Callback_Text::getInstance();
        $model->body();
        $model->wrapper();
    }

    public function testGridViewButton()
    {
        $model = Application_Model_Grid_Callback_ViewButton::getInstance();
        $model->body();
        $model->wrapper();
    }

    public function testGridZeroMoney()
    {
        $model = Application_Model_Grid_Callback_ZeroMoney::getInstance();
        $model->body();
        $model->wrapper();
    }

    //base trait
    public function testOnlyBaseTrait()
    {
        $model = Application_Model_Grid_Callback_ZeroMoney::getInstance();
        $model->body();
        $model->wrapper();
        $model->renderWrapper('', '', '', []);
        //        $newmodel = clone $model;
        //        $s = serialize($model);
        //        $w = unserialize($s);
    }
}
