<?php

class Application_Model_Entity_Collection_Entity_ContractorTemp extends Application_Model_Base_Collection
{
    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_ContractorTemp(),
            'correspondence_method',
            new Application_Model_Entity_Entity_Contact_Type(),
            'id',
            ['correspondence_method_title' => 'title']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_ContractorTemp(),
            'status',
            new Application_Model_Entity_System_ContractorStatus(),
            'id',
            ['status_title' => 'title']
        );

        return $this;
    }

    public function addSettlementGroup(): self
    {
        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_ContractorTemp(),
            'settlement_group_id',
            new Application_Model_Entity_Settlement_Group(),
            'id',
            ['settlement_group' => 'code']
        );

        return $this;
    }

    public function addTempStatusInfo(): self
    {
        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_ContractorTemp(),
            'status_id',
            new Application_Model_Entity_System_PaymentTempStatus(),
            'id',
            ['temp_status_title' => 'title']
        );

        return $this;
    }

    public function addSourceIdFilter(int $sourceId): self
    {
        $this->addFilter('source_id', $sourceId);

        return $this;
    }
}
