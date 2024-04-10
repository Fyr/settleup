<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Deductions_Deduction as Deduction;
use Application_Model_Entity_System_SettlementCycleStatus as CycleStatus;
use Application_Model_Grid_Callback_ActionDeductions as ActionsCallback;
use Application_Model_Grid_Callback_Balance as BalanceCallback;
use Application_Model_Grid_Callback_DateFormat as DateFormatCallback;
use Application_Model_Grid_Callback_DeductionQuickEditAmount as QEAmountCallback;
use Application_Model_Grid_Callback_Frequency as FrequencyCallback;
use Application_Model_Grid_Callback_NegativeMoney as DeductionAmountCallback;
use Application_Model_Grid_Callback_QuickEditAdjustedBalance as BalanceQEAmountCallback;
use Application_Model_Grid_Callback_RecurringCheckbox as CheckboxCallback;
use Application_Model_Grid_Callback_TransactionFee as TransactionFeeCallback;
use Application_Model_Grid_Header_Deductions as GridButtons;

/**
    TODO: Refactor callbacks for grid as we have a huge number of classes for the same quick edit puproses:
    - Application_Model_Grid_Callback_DeductionAdjustedBalance
    - Application_Model_Grid_Callback_DeductionQuickEditAdjustedBalance
    - Application_Model_Grid_Callback_DeductionQuickEditAmount
    - Application_Model_Grid_Callback_DeductionQuickEditQuantity (remove?)
    - Application_Model_Grid_Callback_DeductionQuickEditRate (remove?)
    - Application_Model_Grid_Callback_QuickEditAdjustedBalance (the same as DeductionAdjustedBalance but only for Deductions)
    - Application_Model_Grid_Callback_QuickEditDeductionAmount
    - Application_Model_Grid_Callback_QuickEditQuantity (remove?)
    - Application_Model_Grid_Callback_QuickEditRate (remove?)
*/
class Application_Model_Grid_Deduction_Deduction extends Application_Model_Grid
{
    protected $rewriteColumns = [
        'powerunit.code' => 'powerunit.powerunit_code',
        'contractor.code' => 'contractor.contractor_code',
    ];

    public function __construct()
    {
        $user = User::getCurrentUser();
        $status = $user->getCurrentCycle()->getStatusId();

        $hideCheckbox = ($status == CycleStatus::APPROVED_STATUS_ID
            || ($status == CycleStatus::PROCESSED_STATUS_ID && $user->isOnboarding()));

        $deductionEntity = new Deduction();
        $header = [
            'class' => 'deductions',
            'fixable' => true,
            'header' => $deductionEntity->getResource()->getInfoFields(),
            'sort' => ['id' => 'DESC', 'deduction_code' => 'ASC'],
            'id' => static::class,
            'callbacks' => [
                'checkbox' => CheckboxCallback::class,
                'invoice_date' => DateFormatCallback::class,
                'billing_title' => FrequencyCallback::class,
                'amount' => QEAmountCallback::class,
                'deduction_amount' => DeductionAmountCallback::class,
                'balance' => BalanceCallback::class,
                'adjusted_balance' => BalanceQEAmountCallback::class,
                'transaction_fee' => TransactionFeeCallback::class,
                'action' => ActionsCallback::class,
            ],
            'datePickerFilters' => [
                'invoice_date',
            ],
            'buttons' => GridButtons::class,
            'dragrows' => !$hideCheckbox,
            'filter' => true,
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
            'totals' => [
                'template' => 'deductions/deductions/totals.phtml',
                'data' => [
                    'hideCheckbox' => $hideCheckbox,
                ],
            ],
        ];

        if ($hideCheckbox) {
            $header['checkboxField'] = false;
        }

        $customFilters = [
            'addCarrierFilter',
            'addContractorFilter',
            'addNonDeletedFilter',
        ];

        return parent::__construct(
            $deductionEntity::class,
            $header,
            [],
            $customFilters,
            []
        );
    }
}
