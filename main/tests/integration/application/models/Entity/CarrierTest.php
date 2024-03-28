<?php

class CarrierTest extends BaseTestCase
{
    /**
     * @var Application_Model_Entity_Entity_Carrier
     */
    protected static $_carrier;

    public function testCarrier()
    {
        self::$_carrier = (new Application_Model_Entity_Entity_Carrier())->getCollection()
            ->getFirstItem();
    }

    public function testCarrierGetPayments()
    {
        self::$_carrier->getPayments();
    }

    public function testCarrierGetContractors()
    {
        self::$_carrier->getContractors();
    }

    public function testCarrierGetActiveContractorsArray()
    {
        self::$_carrier->getActiveContractorsArray();
    }

    public function testCarrierGetPreviousSettlementCycle()
    {
        self::$_carrier->getPreviousSettlementCycle();
    }
}
