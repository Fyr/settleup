<?php

class CarrierContractorTest extends BaseTestCase
{
    /**
     * @var Application_Model_Entity_Entity_CarrierContractor
     */
    protected static $_carrierContractor;

    public function testCarrierContractor()
    {
        self::$_carrierContractor = (new Application_Model_Entity_Entity_CarrierContractor());
    }

    public function testGetContractorData()
    {
        self::$_carrierContractor->GetContractorData();
    }

    //    public function testAddContractor()
    //    {
    //        $user = (new Application_Model_Entity_Accounts_User())->load(16);
    //        Application_Model_Entity_Accounts_User::login($user->getId());
    //        $user->setData('last_selected_contractor','1001')->save();
    //        $carrierContractor = (new Application_Model_Entity_Entity_CarrierContractor())->getCollection()->getLastItem();
    //        $carrierContractor->addContractors(array((new Application_Model_Entity_Entity_Contractor())->getCollection()->getFirstItem()->getId()));
    //    }
}
