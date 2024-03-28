<?php

use Application_Model_Entity_Accounts_Reserve_Transaction as Transaction;
use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_System_SettlementCycleStatus as CycleStatus;
use Application_Model_Grid_Callback_ActionSettlementReserveTransaction as ActionsCallback;
use Application_Model_Grid_Callback_DateFormat as DateFormatCallback;
use Application_Model_Grid_Callback_SettlementReserveTransactionBalance as TransactionBalanceCallback;
use Application_Model_Grid_Callback_SettlementReserveTransactionQuickEditAmount as ReserveTrxAmountQuickEditCallback;
use Application_Model_Grid_Callback_SettlementTransactionCheckbox as TrxCheckboxCallback;

class Application_Model_Grid_Settlement_Transaction extends Application_Model_Grid
{
    public function __construct()
    {
        $user = User::getCurrentUser();
        $cycle = $user->getCurrentCycle();
        $contractor = $user->getCurrentContractor();

        $transactionEntity = new Transaction();
        $transactionHeader = [
            'header' => $transactionEntity->getResource()->getInfoFieldsForSettlementGrid(),
            'id' => static::class,
            'title' => 'Reserve Transactions',
            'sortable' => true,
            'sort' => ['created_datetime' => 'ASC'],
            'filter' => true,
            'datePickerFilters' => [
                'created_datetime',
            ],
            'pagination' => false,
            'callbacks' => [
                'action' => ActionsCallback::class,
                'created_datetime' => DateFormatCallback::class,
                'amount' => ReserveTrxAmountQuickEditCallback::class,
                'balance' => TransactionBalanceCallback::class,
                'checkbox' => TrxCheckboxCallback::class,
            ],
            'buttons' => Application_Model_Grid_Header_Transactions::class,
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
            'totals' => [
                'template' => 'settlement/transaction-totals.phtml',
                'data' => [
                    'contractor' => $contractor->getReserveTransactionAmountSum($cycle->getId()),
                ],
            ],
        ];

        if ($cycle->getStatusId() == CycleStatus::APPROVED_STATUS_ID) {
            $transactionHeader['checkboxField'] = false;
        }

        $transactionCustomFilters = [
            'addCarrierFilter',
            'addNonDeletedFilter',
            ['name' => 'addContractorFilter', 'value' => $contractor->getEntityId()],
            ['name' => 'addSettlementFilter', 'value' => $cycle->getId()],
        ];

        if (!$user->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE)) {
            unset($transactionHeader['callbacks']['amount']);
        }

        return parent::__construct(
            $transactionEntity::class,
            $transactionHeader,
            [],
            $transactionCustomFilters,
            []
        );
    }

    public function saveQuickEdit()
    {
        $this->setValue(max($this->getValue(), 0));

        return parent::saveQuickEdit();
    }

    public function setFilter(?array $data): self
    {
        if (isset($data['created_datetime_reserve_transactions'])) {
            $data['created_datetime'] = $data['created_datetime_reserve_transactions'];
            unset($data['created_datetime_reserve_transactions']);
        }

        return parent::setData('filter', $data);
    }
}
