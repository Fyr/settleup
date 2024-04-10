<?php

class Application_Model_Grid_Settlement_Withdrawal extends Application_Model_Grid
{
    public function __construct()
    {
        $reserveAccountContractor = new Application_Model_Entity_Accounts_Reserve_Powerunit();
        $contractor = Application_Model_Entity_Accounts_User::getCurrentUser()->getCurrentContractor();

        $reserveAccountWithdrawalHeader = [
            'header' => $reserveAccountContractor->getResource()->getInfoFields(),
            // 'sort' => ['name' => 'ASC', 'vendor_reserve_code' => 'ASC'],
            'filter' => true,
            'id' => static::class,
            'checkboxField' => 'id',
            'pagination' => false,
            'idSalt' => 'withdrawal',
            'ignoreMassactions' => true,
        ];

        return parent::__construct(
            $reserveAccountContractor::class,
            $reserveAccountWithdrawalHeader,
            [],
            ['addNonDeletedFilter', ['name' => 'addContractorFilter', 'value' => $contractor->getEntityId()]]
        );
    }
}
