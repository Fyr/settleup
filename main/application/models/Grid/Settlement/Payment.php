<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_Payments_Payment as Payment;
use Application_Model_Entity_System_SettlementCycleStatus as CycleStatus;
use Application_Model_Grid_Callback_ActionSettlementPayments as ActionCb;
use Application_Model_Grid_Callback_Amount as AmountCb;
use Application_Model_Grid_Callback_DateFormat as DateFormatCb;
use Application_Model_Grid_Callback_Frequency as FrequencyCb;
use Application_Model_Grid_Callback_Num as NumCb;
use Application_Model_Grid_Callback_QuickEditQuantity as QEQuantityCb;
use Application_Model_Grid_Callback_QuickEditRate as QERateCb;
use Application_Model_Grid_Callback_RecurringCheckbox as RecurringCb;
use Application_Model_Grid_Callback_Taxable as TaxableCb;

class Application_Model_Grid_Settlement_Payment extends Application_Model_Grid
{
    public function __construct()
    {
        $user = User::getCurrentUser();
        $cycle = $user->getCurrentCycle();
        $contractor = $user->getCurrentContractor();
        $contractorData = current($cycle->getSettlementContractors('id', 'asc', null, $contractor->getEntityId()));

        $paymentEntity = new Payment();
        $paymentHeader = [
            'header' => $paymentEntity->getResource()->getSettlementInfoFields(),
            'filter' => true,
            'title' => 'Compensations',
            'id' => static::class,
            'totals' => [
                'template' => 'settlement/payment-totals.phtml',
                'data' => [
                    'contractor' => $contractorData,
                ],
            ],
            'sortable' => true,
            'pagination' => false,
            'callbacks' => [
                'billing_title' => FrequencyCb::class,
                'quantity' => QEQuantityCb::class,
                'rate' => QERateCb::class,
                'amount' => AmountCb::class,
                'action' => ActionCb::class,
                'checkbox' => RecurringCb::class,
                'taxable' => TaxableCb::class,
                'shipment_complete_date' => DateFormatCb::class,
                'loaded_miles' => NumCb::class,
                'empty_miles' => NumCb::class,
            ],
            'buttons' => Application_Model_Grid_Header_Payments::class,
            'sort' => ['id' => 'DESC', 'payment_code' => 'ASC'],
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
        ];

        if ($cycle->getStatusId() == CycleStatus::APPROVED_STATUS_ID) {
            $paymentHeader['checkboxField'] = false;
        }

        $paymentCustomFilters = [
            'addCarrierFilter',
            'addNonDeletedFilter',
            ['name' => 'addContractorFilter', 'value' => $contractor->getEntityId()],
            ['name' => 'addSettlementFilter', 'value' => $cycle->getId()],
        ];

        if (!$user->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE)) {
            unset($paymentHeader['callbacks']['quantity']);
            unset($paymentHeader['callbacks']['rate']);
        }

        return parent::__construct(
            $paymentEntity::class,
            $paymentHeader,
            [],
            $paymentCustomFilters,
            []
        );
    }
}
