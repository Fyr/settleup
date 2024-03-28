<?php

use Application_Model_Entity_System_PowerunitOwnerType as PowerunitOwnerType;

class Application_Model_Grid_Callback_PowerunitOwnerType
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body(): string
    {
        if (!$this->column) {
            return '-';
        }

        return match ((int) $this->column) {
            PowerunitOwnerType::OWNER_TYPE_FA => 'FA',
            PowerunitOwnerType::OWNER_TYPE_CONTRACTOR => 'Contractor',
            default => $this->column,
        };
    }
}
