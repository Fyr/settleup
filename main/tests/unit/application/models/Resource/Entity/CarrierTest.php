<?php

use PHPUnit\Framework\TestCase;

class Resource_Entity_CarrierTest extends TestCase
{
    public function testGetCarrierInfoFields()
    {
        $model = $this->getMockBuilder('Application_Model_Resource_Entity_Carrier')
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        $this->assertIsArray($model->getInfoFields());
    }

    public function testGetCarrierInfoFieldsForPopup()
    {
        $model = $this->getMockBuilder('Application_Model_Resource_Entity_Carrier')
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        $this->assertIsArray($model->getInfoFieldsForPopup());
    }
}
