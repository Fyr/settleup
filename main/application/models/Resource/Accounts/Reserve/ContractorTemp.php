<?php

class Application_Model_Resource_Accounts_Reserve_ContractorTemp extends Application_Model_Base_Resource
{
    protected $_name = 'reserve_account_contractor_temp';

    public function getInfoFields()
    {
        $infoFields = [
            'entity_id' => 'Vendor',
            'account_name' => 'Reserve Account',
            'vendor_reserve_code' => 'Code',
            'description' => 'Description',
            'min_balance' => 'Min. Balance',
            'contribution_amount' => 'Contribution Amount',
            'status_id' => 'Uploaded File Status',
        ];

        return $infoFields;
    }

    public function getParentEntity()
    {
        return null;
        // return new Application_Model_Entity_Accounts_Reserve_Contractor();
    }
}
