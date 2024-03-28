<?php

use PHPUnit\Framework\TestCase;

class Resource_Entity_CarrierContractorTest extends TestCase
{
    public function testGetCarrierInfoFields()
    {
        $model = $this->getMockBuilder('Application_Model_Resource_Entity_CarrierContractor')
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        $this->assertIsArray($model->getInfoFields());
    }
}
