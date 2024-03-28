<?php

class Application_Model_Grid_Transaction_Contribution extends Application_Model_Grid
{
    public function __construct()
    {
        $reserveAccount = new Application_Model_Entity_Accounts_Reserve_Vendor();
        $reserveAccountContributionHeader = [
            'header' => $reserveAccount->getResource()->getInfoFieldsForContributionPopup(),
            'sort' => ['name' => 'ASC', 'vendor_reserve_code' => 'ASC'],
            'filter' => true,
            'id' => static::class,
            'checkboxField' => 'reserve_account_id',
            'pagination' => false,
            'idSalt' => 'contribution',
            'callbacks' => [
                'billing_title' => 'Application_Model_Grid_Callback_Frequency',
                'contribution_amount' => 'Application_Model_Grid_Callback_Balance',
            ],
            'ignoreMassactions' => true,
        ];

        return parent::__construct(
            $reserveAccount::class,
            $reserveAccountContributionHeader,
            [],
            ['addNonDeletedFilter', 'addCarrierVendorFilter']
        );
    }
}
