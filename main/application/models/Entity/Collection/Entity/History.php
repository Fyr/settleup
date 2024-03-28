<?php

class Application_Model_Entity_Collection_Entity_History extends Application_Model_Base_Collection
{
    public function addContractorsTable()
    {
        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_History(),
            'entity_id',
            new Application_Model_Entity_Entity_Contractor(),
            'entity_id',
            ['contractor_entity_id' => 'entity_id', 'carrier_id']
        );

        return $this;
    }

    public function addVendorsTable()
    {
        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_History(),
            'entity_id',
            new Application_Model_Entity_Entity_Vendor(),
            'entity_id',
            ['vendor_entity_id' => 'entity_id', 'carrier_id']
        );

        return $this;
    }

    public function addCarrierFilter($carrierId)
    {
        $this->addFilter('carrier_id', $carrierId);

        return $this;
    }
}
