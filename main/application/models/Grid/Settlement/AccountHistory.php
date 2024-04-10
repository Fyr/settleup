<?php

use Application_Model_Grid_Callback_DateFormat as DateFormatCallback;

class Application_Model_Grid_Settlement_AccountHistory extends Application_Model_Grid
{
    public function __construct()
    {
        $cycle = Application_Model_Entity_Accounts_User::getCurrentUser()->getCurrentCycle();
        $contractor = Application_Model_Entity_Accounts_User::getCurrentUser()->getCurrentContractor();

        $reserveAccountContractorHistory = new Application_Model_Entity_Accounts_Reserve_History();

        $raInfoFields = $reserveAccountContractorHistory->getResource()->getInfoFields();
        if ($cycle->getStatusId() == Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID) {
            $raInfoFields['current_balance'] = 'Ending Balance';
        }
        $reserveAccountsHeader = [
            'header' => $raInfoFields,
            // 'sort' => ['contractor_vendor_reserve_code' => 'ASC'],
            'title' => 'Reserve Accounts',
            'id' => static::class,
            'pagination' => false,
            'sortable' => true,
            'filter' => true,
            'datePickerFilters' => [
                'created_datetime',
            ],
            'checkboxField' => false,
            'callbacks' => [
                'action' => Application_Model_Grid_Callback_ActionReserveAccountHistory::class,
                'current_balance' => Application_Model_Grid_Callback_SettlementCurrentBalance::class,
                'verify_balance' => Application_Model_Grid_Callback_Balance::class,
                'min_balance' => Application_Model_Grid_Callback_Balance::class,
                'created_datetime' => DateFormatCallback::class,
            ],
            'buttons' => Application_Model_Grid_Header_Empty::class,
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'reserve_account_contractor_id',
            ],
            'totals' => [
                'template' => 'settlement/account-totals.phtml',
                'data' => [
                    'contractor' => $contractor->getReserveAccountCurrentBalanceSum($cycle->getId()),
                    'cycle' => $cycle,
                ],
            ],
        ];

        $reserveAccountsCustomFilters = [
            'addNonDeletedFilter',
            ['name' => 'addContractorFilter', 'value' => $contractor->getEntityId()],
            ['name' => 'addSettlementFilter', 'value' => $cycle->getId()],
        ];

        $grid = parent::__construct(
            $reserveAccountContractorHistory::class,
            $reserveAccountsHeader,
            [],
            $reserveAccountsCustomFilters,
            []
        );

        $grid->setSettlementCycle($cycle);

        return $grid;
    }

    public function setFilter(?array $data): self
    {
        if (isset($data['created_datetime_reserve_accounts'])) {
            $data['created_datetime'] = $data['created_datetime_reserve_accounts'];
            unset($data['created_datetime_reserve_accounts']);
        }

        return parent::setData('filter', $data);
    }
}
