<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_Payments_Payment as Payment;
use Application_Model_Entity_System_FileStorageType as FileStorageType;
use Application_Model_Entity_System_SettlementCycleStatus as CycleStatus;
use Application_Model_Grid_Callback_ActionPayments as ActionCallback;
use Application_Model_Grid_Callback_Amount as AmountCallback;
use Application_Model_Grid_Callback_DateFormat as DateFormatCallback;
use Application_Model_Grid_Callback_Frequency as FrequencyCallback;
use Application_Model_Grid_Callback_QuickEditQuantity as QEQuantityCallback;
use Application_Model_Grid_Callback_QuickEditRate as QERateCallback;
use Application_Model_Grid_Callback_RecurringCheckbox as CheckboxCallback;
use Application_Model_Grid_Callback_Taxable as TaxableCallback;

class Application_Model_Grid_Payment_Payment extends Application_Model_Grid
{
    protected $rewriteColumns = [
        'powerunit.code' => 'powerunit.powerunit_code',
        'contractor.code' => 'contractor.contractor_code',
    ];

    public function __construct()
    {
        $user = User::getCurrentUser();
        $cycle = $user->getCurrentCycle();
        //        $contractor = Application_Model_Entity_Accounts_User::getCurrentUser()->getSelectedContractor();
        //        if ($contractor) {
        //            $totalData = $cycle->getSettlementContractors('id', 'asc', null, $contractor->getEntityId());
        //            $totals = ($totalData) ? current($totalData) : false;
        //        } else {
        //            $totals = $cycle->getSettlementContractorsTotal();
        //        };
        $paymentEntity = new Payment();
        $header = [
            'totals' => [
                'template' => 'payments/payments/totals.phtml',
            ],
            'class' => 'payments',
            'fixable' => true,
            'header' => $paymentEntity->getResource()->getInfoFields(),
            'id' => static::class,
            'hideMultiAction' => ($cycle->getStatusId() != CycleStatus::VERIFIED_STATUS_ID),
            'sort' => ['id' => 'DESC', 'payment_code' => 'ASC'],
            'callbacks' => [
                'checkbox' => CheckboxCallback::class,
                'billing_title' => FrequencyCallback::class,
                'taxable' => TaxableCallback::class,
                'quantity' => QEQuantityCallback::class,
                'shipment_complete_date' => DateFormatCallback::class,
                'rate' => QERateCallback::class,
                'amount' => AmountCallback::class,
                'action' => ActionCallback::class,
            ],
            'idField' => 'id',
            'buttons' => 'Application_Model_Grid_Header_Payments',
            'filter' => true,
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
        ];
        if ($cycle->getStatusId() == CycleStatus::APPROVED_STATUS_ID) {
            $header['checkboxField'] = false;
        }

        $button = [];
        $massaction = [];
        if ($user->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE)) {
            $massaction = [
                "delete" => [
                    "caption" => "Delete Selected",
                    "button_class" => "btn-danger confirm-delete btn-multiaction",
                    "confirm-type" => "Deletion",
                    "icon_class" => "icon-trash",
                    "action-type" => "delete",
                    "url" => '/payments_payments/multiaction',
                ],
            ];
            $button['add'] = [
                "caption" => "Add",
                "button_class" => "btn-success",
                "icon_class" => "icon-plus",
                "url" => "#popup_checkbox_modal",
                "data-toggle" => "modal",
                'data-target' => '.popup_checkbox_modal',
            ];
        } else {
            unset($header['callbacks']['quantity']);
            unset($header['callbacks']['rate']);
        }
        if ($user->hasPermission(Permissions::UPLOADING)) {
            $button['upload'] = [
                'caption' => 'Upload',
                'button_class' => 'btn-success',
                'icon_class' => 'icon-file',
                'url' => '/file_index/edit/' . FileStorageType::CONST_PAYMENTS_FILE_TYPE,
            ];
            $button['download'] = [
                'caption' => 'Download',
                'button_class' => 'btn-success',
                'icon_class' => 'icon-file',
                'url' => '/file_index/export/' . FileStorageType::CONST_PAYMENTS_FILE_TYPE,
            ];
        }

        $customFilters = [
            'addCarrierFilter',
            'addContractorFilter',
            'addNonDeletedFilter',
        ];

        return parent::__construct(
            $paymentEntity::class,
            $header,
            $massaction,
            $customFilters,
            $button
        );
    }
}
