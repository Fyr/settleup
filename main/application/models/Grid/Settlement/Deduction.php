<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Deductions_Deduction as Deduction;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_System_SettlementCycleStatus as CycleStatus;
use Application_Model_Grid_Callback_ActionSettlementDeductions as ActionsCallback;
use Application_Model_Grid_Callback_Balance as BalanceCallback;
use Application_Model_Grid_Callback_DateFormat as DateFormatCallback;
use Application_Model_Grid_Callback_Frequency as FrequencyCallback;
use Application_Model_Grid_Callback_QuickEditDeductionAmount as QEAmountCallback;
use Application_Model_Grid_Callback_RecurringCheckbox as CheckboxCallback;
use Application_Model_Grid_Callback_TransactionFee as TransactionFeeCallback;
use Application_Model_Grid_Header_Deductions as DeductionsHeader;

class Application_Model_Grid_Settlement_Deduction extends Application_Model_Grid
{
    public function __construct()
    {
        $user = User::getCurrentUser();
        $cycle = $user->getCurrentCycle();
        $contractor = $user->getCurrentContractor();
        $contractorData = current($cycle->getSettlementContractors('id', 'asc', null, $contractor->getEntityId()));
        $deductionEntity = new Deduction();

        $deductionHeader = [
            'header' => $deductionEntity->getResource()->getSettlementInfoFields(),
            'sort' => ['id' => 'DESC', 'deduction_code' => 'ASC'],
            'id' => static::class,
            'callbacks' => [
                'checkbox' => CheckboxCallback::class,
                'invoice_date' => DateFormatCallback::class,
                'billing_title' => FrequencyCallback::class,
                'amount' => QEAmountCallback::class,
                'deduction_amount' => QEAmountCallback::class,
                'balance' => BalanceCallback::class,
                'adjusted_balance' => BalanceCallback::class,
                'action' => ActionsCallback::class,
                'transaction_fee' => TransactionFeeCallback::class,
            ],
            'datePickerFilters' => [
                'invoice_date',
            ],
            'buttons' => DeductionsHeader::class,
            'dragrows' => ($cycle->getStatusId() < CycleStatus::PROCESSED_STATUS_ID),
            'filter' => true,
            // 'priorityFilterField' => 'contractor_id',
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
            'title' => 'Deductions',
            'totals' => [
                'template' => 'settlement/deduction-totals.phtml',
                'data' => [
                    'contractor' => $contractorData,
                ],
            ],
            'sortable' => ($cycle->getStatusId() >= CycleStatus::PROCESSED_STATUS_ID),
            'pagination' => false,
        ];

        if ($cycle->getStatusId() == CycleStatus::APPROVED_STATUS_ID) {
            $deductionHeader['checkboxField'] = false;
        }

        $deductionCustomFilters = [
            'addCarrierFilter',
            'addNonDeletedFilter',
            ['name' => 'addContractorFilter', 'value' => $contractor->getEntityId()],
            ['name' => 'addSettlementFilter', 'value' => $cycle->getId()],
        ];

        if (!$user->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE)) {
            unset($deductionHeader['callbacks']['amount']);
            unset($deductionHeader['callbacks']['balance']);
        }

        return parent::__construct(
            $deductionEntity::class,
            $deductionHeader,
            [],
            $deductionCustomFilters,
            []
        );
    }

    public function setFilter(?array $data): self
    {
        if (isset($data['invoice_date_deductions'])) {
            $data['invoice_date'] = $data['invoice_date_deductions'];
            unset($data['invoice_date_deductions']);
        }

        return parent::setData('filter', $data);
    }
}
