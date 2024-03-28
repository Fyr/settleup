<?php

use PHPUnit\Framework\TestCase;

class Resource_Entity_VendorTest extends TestCase
{
    public function testGetContractorInfoFields()
    {
        $model = $this->getMockBuilder('Application_Model_Resource_Entity_Vendor')
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        $this->assertIsArray($model->getInfoFieldsForPopup());
    }
}
