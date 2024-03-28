<?php

class Entity_Collection_PaymentsTest extends BaseTestCase
{
    public function testEntityCollectionContactInfo()
    {
        $entity = (new Application_Model_Entity_Payments_Payment());
        $collection = new Application_Model_Entity_Collection_Payments_Payment($entity);
        $collection->load();

        $collection->getAmount();
    }

    public function testEntityCollectionPayments()
    {
        $entity = (new Application_Model_Entity_Payments_Temp());
        $collection = new Application_Model_Entity_Collection_Payments_Temp($entity);
        $collection->load();
    }
}
