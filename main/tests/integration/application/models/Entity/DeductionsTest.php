<?php

class DeductionsTest extends BaseTestCase
{
    /**
     * @var Application_Model_Entity_Settlement_Cycle
     */
    protected static $_cycle;
    /**
     * @var Application_Model_Entity_Entity_Contractor
     */
    protected static $_contractor;
    /**
     * @var Application_Model_Entity_Deductions_Setup
     */
    protected static $_entityDeductionSetup;
    /**
     * @var Application_Model_Entity_Deductions_Deduction
     */
    protected static $_entityDeduction;

    public function testCycleDeduction()
    {
        Application_Model_Entity_Accounts_User::login($this->_myUser);
        $carrier = $this->newCarrier();
        self::$_contractor = $this->newContractor($carrier);
        self::$_cycle = $this->newCycle($carrier);
        self::$_entityDeductionSetup = $this->newDeductionSetup($carrier);

        (new Application_Model_Entity_Deductions_Deduction())->setData(
            [
                'setup_id' => self::$_entityDeductionSetup->getId(),
                'provider_id' => $carrier->getEntityId(),
                'contractor_id' => self::$_contractor->getEntityId(),
                'carrier_id' => $carrier->getEntityId(),
                'rate' => null,
                'quantity' => '1',
                'recurring' => '0',
                'terms' => '1',
                'settlement_cycle_id' => self::$_cycle->getId(),
                'billing_cycle_id' => '1',
            ]
        )
            ->save();
        self::$_entityDeduction = (new Application_Model_Entity_Deductions_Deduction())->getCollection()
            ->getLastItem();
    }

    //    public function testDeleteRecurringDeductions()
    //    {
    //        self::$_entityDeduction->deleteRecurringDeductions();
    //    }

    public function testSetPriority()
    {
        self::$_entityDeduction->setPriority([self::$_entityDeduction->getId() => '1']);
    }

    public function testGetReserveAccountContractor()
    {
        self::$_entityDeduction->getReserveAccountContractor();
    }

    public function testGetContractor()
    {
        self::$_entityDeduction->getContractor();
    }

    public function testGetProviderName()
    {
        self::$_entityDeduction->getProviderName();
    }

    public function testGetWithdrawalAmount()
    {
        self::$_entityDeduction->getWithdrawalAmount();
    }

    public function testGetFuturePriority()
    {
        self::$_entityDeduction->getFuturePriority();
    }

    public function testSaveWithPriority()
    {
        self::$_entityDeduction->saveWithPriority('1');
    }

    public function testGetDiffBalance()
    {
        self::$_entityDeduction->getDiffBalance();
    }

    public function testGetProvider()
    {
        self::$_entityDeduction->getProvider();
    }

    public function testGetCarrierCollection()
    {
        self::$_entityDeduction->getCarrierCollection();
    }

    public function testGetVendorCollection()
    {
        self::$_entityDeduction->getVendorCollection();
    }

    //    public function testCreateNewDeduction()
    //    {
    //        self::$_entityDeduction->createNewDeduction();
    //    }

    public function testGetRecurringStrategy()
    {
        self::$_entityDeduction->getRecurringStrategy();
    }

    //    public function testCreate()
    //    {
    //        Application_Model_Entity_Accounts_User::login(16);
    //        self::$_entityDeduction->create(self::$_entityDeductionSetup->getId(), self::$_contractor->getEntityId(), self::$_cycle->getId());
    //    }

    public function testGetDeductionAmount()
    {
        self::$_entityDeduction->getDeductionAmount();
    }

    //setup deductions
    public function testSetupSetPriority()
    {
        self::$_entityDeductionSetup->setPriority([self::$_entityDeductionSetup->getId() => '1']);
    }

    public function testSetupGetCarrierCollection()
    {
        self::$_entityDeductionSetup->getCarrierCollection();
    }

    //recurring
    //    public function testRecurringEntity()
    //    {
    //        $recurring = new Application_Model_Entity_Deductions_Recurring();
    //        $recurring->getDeduction();
    //        $recurring->apply(self::$_cycle);
    //    }

    public function testTempCheck()
    {
        $temp = new  Application_Model_Entity_Deductions_Temp();
        $temp->check();
        $temp->getControllerName();
        //        $temp->save();
    }
}
