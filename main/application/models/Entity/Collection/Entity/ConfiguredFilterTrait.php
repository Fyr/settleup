<?php

trait Application_Model_Entity_Collection_Entity_ConfiguredFilterTrait
{
    public function addConfiguredFilter($status = Application_Model_Entity_System_SystemValues::CONFIGURED_STATUS)
    {
        $this->addFilter('status', $status);

        return $this;
    }
}
