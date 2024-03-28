<?php

class Application_Model_Grid_Settlement_Contribution extends Application_Model_Grid
{
    public function __construct()
    {
        $reserveAccountContractor = new Application_Model_Entity_Accounts_Reserve_Contractor();
        $contractor = Application_Model_Entity_Accounts_User::getCurrentUser()->getCurrentContractor();

        $reserveAccountContributionHeader = [
            'header' => $reserveAccountContractor->getResource()->getInfoFieldsForSettlementContributionPopup(),
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
            $reserveAccountContractor::class,
            $reserveAccountContributionHeader,
            [],
            ['addNonDeletedFilter', ['name' => 'addContractorFilter', 'value' => $contractor->getEntityId()]]
        );
    }
}
