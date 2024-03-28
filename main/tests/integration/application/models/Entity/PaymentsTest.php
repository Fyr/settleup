<?php

class PaymentsTest extends BaseTestCase
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
     * @var Application_Model_Entity_Payments_Payment
     */
    protected static $_entityPayment;
    /**
     * @var Application_Model_Entity_Payments_Setup
     */
    protected static $_entityPaymentSetup;

    public function testCyclePayment()
    {
        Application_Model_Entity_Accounts_User::login(16);
        $carrier = $this->newCarrier();
        self::$_contractor = $this->newContractor($carrier);
        self::$_cycle = $this->newCycle($carrier);
        self::$_entityPaymentSetup = $this->newPaymentSetup($carrier);

        (new Application_Model_Entity_Payments_Payment())->setData(
            [
                'setup_id' => self::$_entityPaymentSetup->getId(),
                'contractor_id' => self::$_contractor->getEntityId(),
                'carrier_id' => null,
                'rate' => null,
                'quantity' => '1',
                'recurring' => '0',
                'terms' => '1',
                'settlement_cycle_id' => self::$_cycle->getId(),
                'billing_cycle_id' => '1',
            ]
        )
            ->save();
        self::$_entityPayment = (new Application_Model_Entity_Payments_Payment())->getCollection()
            ->getLastItem();
    }

    //    public function testDeleteRecurringPayments()
    //    {
    //        self::$_entityPayment->deleteRecurringPayments();
    //    }

    public function testGetCarrier()
    {
        self::$_entityPayment->getCarrier();
    }

    //    public function testCreateNewPayment()
    //    {
    //        self::$_entityPayment->createNewPayment();
    //    }

    public function testGetRecurringStrategy()
    {
        self::$_entityPayment->getRecurringStrategy();
    }

    //    public function testGetNewRecurringEntity()
    //    {
    //        self::$_entityPayment->getNewRecurringEntity();
    //    }

    //recurring
    //    public function testRecurringEntity()
    //    {
    //        $recurring = new Application_Model_Entity_Payments_Recurring();
    //        $recurring->getPayment();
    //        $recurring->apply(self::$_cycle);
    //    }

}
