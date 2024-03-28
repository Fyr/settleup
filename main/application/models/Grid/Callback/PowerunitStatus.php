<?php

use Application_Model_Entity_System_PowerunitStatus as PowerunitStatus;

class Application_Model_Grid_Callback_PowerunitStatus
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body(): string
    {
        if (!$this->column) {
            return '-';
        }

        return match ((int) $this->column) {
            PowerunitStatus::STATUS_ACTIVE => 'Active',
            PowerunitStatus::STATUS_INACTIVE => 'Inactive',
            PowerunitStatus::STATUS_UNAVAILABLE => 'Unavailable',
            default => $this->column,
        };
    }
}
