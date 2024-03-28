<?php

use Application_Model_Entity_System_ContractorStatus as ContractorStatus;

class Application_Model_Grid_Callback_ContractorStatus
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body(): string
    {
        if (!$this->column) {
            return '-';
        }

        return match ($this->column) {
            ContractorStatus::STATUS_ACTIVE => 'Active',
            ContractorStatus::STATUS_TERMINATED => 'Terminated',
            default => $this->column,
        };
    }
}
