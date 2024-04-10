<?php

use Application_Model_Entity_Accounts_Reserve_Transaction as ReserveTransaction;
use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_System_SettlementCycleStatus as CycleStatus;
// use Application_Model_Grid_Callback_QuickEditAmount as QEAmountCallback;
use Application_Model_Grid_Callback_ActionReserveTransaction as ActionCallback;
use Application_Model_Grid_Callback_DateFormat as DateFormatCallback;
use Application_Model_Grid_Callback_SettlementReserveTransactionQuickEditAmount as QEAmountCallback;
use Application_Model_Grid_Callback_SettlementTransactionCheckbox as CheckboxCallback;

class Application_Model_Grid_Transaction_Transaction extends Application_Model_Grid
{
    public function __construct()
    {
        $user = User::getCurrentUser();
        $cycle = $user->getCurrentCycle();
        $transactionEntity = new ReserveTransaction();

        $hideCheckboxes = ($cycle->getStatusId() == CycleStatus::APPROVED_STATUS_ID || $user->isOnboarding());

        $header = [
            'header' => $transactionEntity->getResource()->getInfoFields(),
            'sort' => [
                'contractor_id' => 'ASC',
            ],
            'dragrows' => !$hideCheckboxes,
            'filter' => true,
            'id' => static::class,
            'checkboxField' => 'id',
            'callbacks' => [
                'checkbox' => CheckboxCallback::class,
                'created_datetime' => DateFormatCallback::class,
                'amount' => Application_Model_Grid_Callback_MoneyReadOnly::class,
                'adjusted_balance' => Application_Model_Grid_Callback_MoneyReadOnly::class,
                'balance' => Application_Model_Grid_Callback_MoneyReadOnly::class,
                'action' => ActionCallback::class,
            ],
            'buttons' => 'Application_Model_Grid_Header_Transactions',
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
        ];

        if ($hideCheckboxes) {
            $header['checkboxField'] = false;
        }

        if (!$user->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE) || $user->isOnboarding()) {
            $header['callbacks']['amount'] = 'Application_Model_Grid_Callback_Balance';
        }

        $customFilters = ['addCarrierFilter', 'addContractorFilter', 'addNonDeletedFilter'];

        return parent::__construct(
            $transactionEntity::class,
            $header,
            [],
            $customFilters,
            []
        );
    }

    public function saveQuickEdit()
    {
        $this->setValue(max($this->getValue(), 0));

        return parent::saveQuickEdit();
    }
}
