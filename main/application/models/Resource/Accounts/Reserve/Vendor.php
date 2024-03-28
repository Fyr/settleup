<?php

class Application_Model_Resource_Accounts_Reserve_Vendor extends Application_Model_Base_Resource
{
    protected $_name = 'reserve_account_vendor';

    public function getInfoFields()
    {
        $infoFields = [
            'priority' => 'Priority',
            'name' => 'Vendor',
            'account_name' => 'Reserve Account',
            'vendor_reserve_code' => 'Code',
            'description' => 'Description',
            'min_balance' => 'Min. Balance',
            'contribution_amount' => 'Contribution Amount',
            'current_balance' => 'Current Balance',
        ];

        return $infoFields;
    }

    public function getInfoFieldsForReport()
    {
        if (!Application_Model_Entity_Accounts_User::getCurrentUser()->isVendor()) {
            $fields = [
                'name' => 'Vendor',
                'account_name' => 'Reserve Account',
                'description' => 'Description',
            ];
        } else {
            $fields = [
                'account_name' => 'Reserve Account',
                'description' => 'Description',
            ];
        }

        return $fields;
    }

    public function getInfoFieldsForContributionPopup()
    {
        $infoFields = [
            'name' => 'Vendor',
            'vendor_reserve_code' => 'Code',
            'description' => 'Description',
            'contribution_amount' => 'Contribution Amount',
        ];

        return $infoFields;
    }

    public function getInfoFieldsForWithdrawalPopup()
    {
        $infoFields = [
            'name' => 'Vendor',
            'vendor_reserve_code' => 'Code',
            'description' => 'Description',
        ];

        return $infoFields;
    }

    public function getSetupFields()
    {
        return [
            'description',
            'min_balance',
            'contribution_amount',
            'vendor_reserve_code',
            'priority',
            'account_name',
        ];
    }
}
