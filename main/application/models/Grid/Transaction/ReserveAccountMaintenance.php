<?php

class Application_Model_Grid_Transaction_ReserveAccountMaintenance extends Application_Model_Grid
{
    public function __construct()
    {
        $reserveAccount = new Application_Model_Entity_Accounts_Reserve_Powerunit();
        $reserveAccountContributionHeader = [
            'header' => $reserveAccount->getResource()->getInfoFieldsShort(),
            'filter' => true,
            'id' => static::class,
            'checkboxField' => 'id',
            'pagination' => false,
            'idSalt' => 'withdrawal',
            'callbacks' => [
                'account_type' => 'Application_Model_Grid_Callback_ReserveAccountType',
            ],
            'ignoreMassactions' => true,
        ];

        return parent::__construct(
            $reserveAccount::class,
            $reserveAccountContributionHeader,
            [],
            [
                'addNonDeletedFilter',
                'addMaintenanceFilter',
            ]
        );
    }
}
