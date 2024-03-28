<?php

class Application_Model_Resource_Accounts_Reserve_Carrier extends Application_Model_Base_Resource
{
    protected $_name = 'reserve_account_carrier';

    public function getInfoFields()
    {
        $infoFields = [
            $this->getPrimaryKey() => '#',
            'account_name' => 'Account Name',
            'name' => 'Carrier',
            'description' => 'Description',
            'contribution_amount' => 'Contribution Amount',
            'current_balance' => 'Current Balance',
        ];

        return $infoFields;
    }
}
