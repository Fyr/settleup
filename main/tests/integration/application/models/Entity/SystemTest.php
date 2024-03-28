<?php

class SystemTest extends BaseTestCase
{
    public function testSettlementCycleResource()
    {
        $model = new Application_Model_Entity_Settlement_Cycle();

        $this->assertEquals(
            is_array(
                $model->getResource()
                    ->getInfoFields()
            ),
            true
        );
    }

    //    public function testSettlementCycleDefaultPeriodResource()
    //    {
    //        $model = new Application_Model_Entity_System_CyclePeriod();
    //        $date =  new Zend_Date();
    //        try{
    //            $model->getPeriodLength($date);
    //        } catch (Exception $expected) {
    //            $this->assertTrue(true);
    //            return;
    //        }
    //        $this->fail('There should be a Exception!Undefined cycle period');
    //    }

    public function testSettlementgetAdvancedFields()
    {
        $model = new Application_Model_Resource_Settlement_Cycle();
        $model->getAdvancedInfoFields();
    }

    public function testResource_Payments_Temp()
    {
        $model = new Application_Model_Resource_Payments_Temp();
        $model->getInfoFields();
        $model->getParentEntity();
    }

    public function testResource_Deductions_Temp()
    {
        $model = new Application_Model_Resource_Deductions_Temp();
        $model->getInfoFields();
        $model->getParentEntity();
    }

    public function testResource_Accounts_Reserve_Contractor()
    {
        $model = new Application_Model_Resource_Accounts_Reserve_Contractor();
        $model->getInfoFieldsForSettlementGrid();
    }

    public function testResource_Entity_Contact_Info()
    {
        $model = new Application_Model_Resource_Entity_Contact_Info();
        $model->getContactsByEntity(
            (new Application_Model_Entity_Entity_Contact_Info())->getCollection()
                ->getFirstItem()
        );
    }

    public function testResource_Entity_ContractorVendor()
    {
        $model = new Application_Model_Resource_Entity_ContractorVendor();
        $model->getInfoFields();
        $model->getVendorsByContractorId('1');
    }

    public function testResource_Settlement_Rule()
    {
        $model = new Application_Model_Resource_Settlement_Rule();
        $model->getInfoFields();
    }

    public function testModelSystem_Daysofweek()
    {
        $class = new Application_Model_System_Daysofweek();
        $class->getList();
        $class->getDayByNumber(1);
    }

    public function testEntitySystemCyclePeriod()
    {
        $class = new Application_Model_Entity_System_CyclePeriod();
        $class->getBillingCycles(Application_Model_Entity_System_CyclePeriod::MONTHLY_SEMI_MONTHLY_ID);
    }
}
