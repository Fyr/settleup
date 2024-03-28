<?php

class DisbursementTest extends BaseTestCase
{
    /**
     * @var Application_Model_Entity_Transactions_Disbursement
     */
    protected static $_disbursement;

    public function testDisbursement()
    {
        self::$_disbursement = (new Application_Model_Entity_Transactions_Disbursement())->getCollection()
            ->getFirstItem();
    }

    public function testSave()
    {
        self::$_disbursement->setData('source_id', '0')
            ->save();
    }

    public function testDefault()
    {
        self::$_disbursement->setData('created_by')
            ->save();
        self::$_disbursement->getDefaultData();
    }

    public function testGetEntity()
    {
        self::$_disbursement->getEntity();
    }

    public function testGetType()
    {
        self::$_disbursement->getType();
    }

    public function testGetCycle()
    {
        self::$_disbursement->getCycle();
    }
}
