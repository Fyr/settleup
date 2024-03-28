<?php

use PHPUnit\Framework\TestCase;

class Resource_Entity_ContractorTest extends TestCase
{
    public function testGetContractorInfoFields()
    {
        $model = $this->getMockBuilder('Application_Model_Resource_Entity_Contractor')
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        $this->assertIsArray($model->getInfoFields());
    }

    public function testGetContractorInfoFieldsForReportPopup()
    {
        $model = $this->getMockBuilder('Application_Model_Resource_Entity_Contractor')
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        $this->assertIsArray($model->getInfoFieldsForReportPopup());
    }
}
