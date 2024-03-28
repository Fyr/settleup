<?php

use Application_Model_Entity_System_BookkeepingType as BookkeepingType;

class Application_Model_Grid_Callback_ContractorBookkeepingType
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body(): string
    {
        if (!$this->column) {
            return '-';
        }

        return match ((int)$this->column) {
            BookkeepingType::TYPE_ATBS => 'ATBS',
            BookkeepingType::TYPE_EQUINOX => 'Equinox',
            default => $this->column,
        };
    }
}
