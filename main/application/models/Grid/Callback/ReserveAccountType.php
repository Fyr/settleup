<?php

class Application_Model_Grid_Callback_ReserveAccountType
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body(): string
    {
        if ($this->column == Application_Model_Entity_System_ReserveAccountType::ESCROW_ACCOUNT) {
            return 'Escrow';
        }

        if ($this->column == Application_Model_Entity_System_ReserveAccountType::MAINTENANCE_ACCOUNT) {
            return 'Maintenance';
        }

        return '-';
    }
}
