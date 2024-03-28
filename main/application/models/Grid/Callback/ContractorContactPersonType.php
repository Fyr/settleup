<?php

use Application_Model_Entity_System_ContractorPersonType as ContractorPersonType;

class Application_Model_Grid_Callback_ContractorContactPersonType
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body(): string
    {
        if (!$this->column) {
            return '-';
        }

        return match ((int)$this->column) {
            ContractorPersonType::TYPE_OWNER => 'Owner',
            ContractorPersonType::TYPE_REPRESENTATIVE => 'Representative',
            default => $this->column,
        };
    }
}
