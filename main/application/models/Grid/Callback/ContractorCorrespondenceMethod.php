<?php

use Application_Model_Entity_Entity_Contact_Type as ContactType;

class Application_Model_Grid_Callback_ContractorCorrespondenceMethod
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body(): string
    {
        if (!$this->column) {
            return '-';
        }

        return match ((int)$this->column) {
            ContactType::TYPE_EMAIL => 'Yes',
            ContactType::TYPE_CARRIER_DISTRIBUTES => 'No',
            default => $this->column,
        };
    }
}
