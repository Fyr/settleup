<?php

class SettlementCycleTest extends BaseTestCase
{
    protected static $_entityCycle;

    public function testCycle()
    {
        self::$_entityCycle = (new Application_Model_Entity_Settlement_Cycle())->setData(
            [
                'carrier_id' => '1',
                'cycle_period_id' => '1',
                'cycle_start_date' => '2010-01-18',
                'cycle_close_date' => '2010-01-21',
                'disbursement_terms' => '1',
                'payment_terms' => '1',
                'status_id' => '1',
            ]
        )
            ->save();
    }

    public function testGetSettlementCycle()
    {
        self::$_entityCycle->getSettlementCycle();
    }

    //    public function testGetSettlementDayWord()
    //    {
    //        self::$_entityCycle->getSettlementDayWord();
    //    }

    public function testGetPayments()
    {
        self::$_entityCycle->getPayments([Application_Model_Entity_Settlement_Cycle::ALL_FILTER_TYPE_ASC]);
    }

    //    public function testHasNotApprovedPayments()
    //    {
    //        self::$_entityCycle->hasNotApprovedPayments();
    //    }

    public function testGetDeductions()
    {
        self::$_entityCycle->getDeductions([Application_Model_Entity_Settlement_Cycle::ALL_FILTER_TYPE_ASC]);
    }

    public function testGetDisbursement()
    {
        self::$_entityCycle->getDisbursement();
    }

    public function testGetDisbursementRoutingCheckSum()
    {
        self::$_entityCycle->getDisbursementRoutingCheckSum();
    }

    public function testGetDisbursementAmountSum()
    {
        self::$_entityCycle->getDisbursementAmountSum();
    }

    //    public function testHasNotApprovedDeductions()
    //    {
    //        self::$_entityCycle->hasNotApprovedDeductions();
    //    }

    public function testGetReserveTransactions()
    {
        self::$_entityCycle->getReserveTransactions([Application_Model_Entity_Settlement_Cycle::ALL_FILTER_TYPE_ASC]);
    }

    public function testGetWithdrawals()
    {
        self::$_entityCycle->getWithdrawals();
    }

    public function testGetContributions()
    {
        self::$_entityCycle->getContributions();
    }

    //    public function testHasNotApprovedReserveTransaction()
    //    {
    //        self::$_entityCycle->hasNotApprovedReserveTransaction();
    //    }

    //    public function testIsFullyFunded()
    //    {
    //        self::$_entityCycle->isFullyFunded();
    //    }

    public function testGetSettlementContractors()
    {
        self::$_entityCycle->getSettlementContractors(
            'id',
            'ASC',
            [Application_Model_Entity_Settlement_Cycle::ALL_FILTER_TYPE_ASC]
        );
    }

    public function testGetSettlementContractorsTotal()
    {
        self::$_entityCycle->getSettlementContractorsTotal(
            'id',
            'ASC',
            Application_Model_Entity_Settlement_Cycle::ARCHIVE_FILTER_TYPE
        );
    }

    public function testGetTotal()
    {
        self::$_entityCycle->getTotal('name');
    }

    public function testGetResult()
    {
        self::$_entityCycle->setStatusId(Application_Model_Entity_System_SettlementCycleStatus::NOT_VERIFIED_STATUS_ID)
            ->save();
        self::$_entityCycle->getResult();
    }

    public function testIsFullyApproved()
    {
        self::$_entityCycle->isFullyApproved();
    }

    public function testGetVendors()
    {
        self::$_entityCycle->getVendors(new Application_Model_Entity_Entity_Contractor());
    }

    public function testGetContractorsArray()
    {
        self::$_entityCycle->getContractorsArray();
    }

    public function testIsFirstCycle()
    {
        self::$_entityCycle->isFirstCycle();
    }

    public function testGetAllCyclePeriods()
    {
        self::$_entityCycle->getAllCyclePeriods();
    }

    public function testGetCyclesFilteredByType()
    {
        self::$_entityCycle->getCyclesFilteredByType();
    }

    public function testGetContractorReserveAccounts()
    {
        self::$_entityCycle->getContractorReserveAccounts();
    }

    public function testApplyRecurringPayments()
    {
        self::$_entityCycle->applyRecurringPayments();
    }

    public function testApplyRecurringDeductions()
    {
        self::$_entityCycle->applyRecurringDeductions();
    }

    public function testCheckCurrentStatusException($action, $exceptionStatus)
    {
        $this->setExpectedException('Exception');
        self::$_entityCycle->setStatusId($exceptionStatus)
            ->save();
        self::$_entityCycle->checkCurrentStatus($action);
    }

    public function checkCurrentStatusProvider()
    {
        return [
            ['process', '1'],
            ['clear', '1'],
            ['verify', '2'],
            ['approve', '1'],
            ['reject', '1'],
            ['close', '1'],
            ['default', '1'],
            ['delete', '1'],
        ];
    }

    public function testDelete()
    {
        Application_Model_Entity_Accounts_User::login(16);
        (new Application_Model_Entity_Accounts_User())->load(16)
            ->setData('last_selected_carrier', '1')
            ->save();
        self::$_entityCycle->setStatusId('2')
            ->save();
        self::$_entityCycle->delete();
    }
}
