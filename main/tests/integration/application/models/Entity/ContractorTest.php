<?php

class ContractorTest extends BaseTestCase
{
    /**
     * @var Application_Model_Entity_Entity_Contractor
     */
    protected static $_contractor;

    public function testContractor()
    {
        self::$_contractor = (new Application_Model_Entity_Entity_Contractor())->getCollection()
            ->getFirstItem();
    }

    public function testGetDeductions()
    {
        self::$_contractor->getDeductions();
    }

    public function testGetCarrier()
    {
        self::$_contractor->getCarrier();
    }

    public function testGetCurrentContractor()
    {
        $user = (new Application_Model_Entity_Accounts_User())->load(16);
        Application_Model_Entity_Accounts_User::login($user->getId());
        self::$_contractor->getCurrentContractor();
        $user->setData('last_selected_contractor')
            ->save();
        self::$_contractor->getCurrentContractor();
    }

    public function testChangeStatus()
    {
        $contractor = (new Application_Model_Entity_Entity_Contractor())->getCollection()
            ->getFirstItem();
        $contractor->setData('id');
        $contractor->setData('start_date');
        $contractor->setData('entity_id');
        $contractor->setData('code', random_int(1, 32000));
        $contractor->changeStatus();
    }
}
