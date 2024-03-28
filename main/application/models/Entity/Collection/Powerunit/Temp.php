<?php

class Application_Model_Entity_Collection_Powerunit_Temp extends Application_Model_Base_Collection
{
    public function addTempStatusInfo(): self
    {
        $this->addFieldsForSelect(
            new Application_Model_Entity_Powerunit_Temp(),
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
