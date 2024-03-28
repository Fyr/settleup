<?php

class Application_Model_Grid_Transaction_Withdrawal extends Application_Model_Grid
{
    public function __construct()
    {
        $reserveAccount = new Application_Model_Entity_Accounts_Reserve_Vendor();
        $reserveAccountWithdrawalHeader = [
            'header' => $reserveAccount->getResource()->getInfoFieldsForWithdrawalPopup(),
            'sort' => ['name' => 'ASC', 'vendor_reserve_code' => 'ASC'],
            'filter' => true,
            'id' => static::class,
            'checkboxField' => 'reserve_account_id',
            'pagination' => false,
            'idSalt' => 'withdrawal',
            'callbacks' => [
                'billing_title' => 'Application_Model_Grid_Callback_Frequency',
            ],
            'ignoreMassactions' => true,
        ];

        return parent::__construct(
            $reserveAccount::class,
            $reserveAccountWithdrawalHeader,
            [],
            ['addNonDeletedFilter', 'addCarrierVendorFilter']
        );
    }
}
