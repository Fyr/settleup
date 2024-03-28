<?php

class ContractorVendorTest extends BaseTestCase
{
    /**
     * @var Application_Model_Entity_Entity_ContractorVendor
     */
    protected static $_contractorVendor;

    public function testCarrierContractor()
    {
        self::$_contractorVendor = (new Application_Model_Entity_Entity_ContractorVendor());
    }

    public function testGetContractorData()
    {
        self::$_contractorVendor->GetContractorData();
    }

    //    public function testAddContractor()
    //    {
    //        self::$_contractorVendor = (new Application_Model_Entity_Entity_ContractorVendor())->getCollection()->getFirstItem();
    //        self::$_contractorVendor->addContractors(array((new Application_Model_Entity_Entity_Contractor())->getCollection()->getLastItem()->getId()));
    //    }

}
