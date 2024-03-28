<?php

use Application_Model_Entity_Accounts_Reserve_Contractor as ReserveContractor;
use Application_Model_Entity_Accounts_Reserve_Transaction as ReserveTransaction;
use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Deductions_Deduction as Deduction;
use Application_Model_Entity_Entity_Carrier as Carrier;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Payments_Payment as Payment;
use Application_Model_Entity_Settlement_Cycle as SettlementCycle;
use Application_Model_Entity_System_ReserveTransactionTypes as ReserveTrxTypes;
use Application_Model_Grid_Callback_Balance as BalanceCb;
use Application_Model_Grid_Callback_DateFormat as DateFormatCb;
use Application_Model_Grid_Callback_DeductionBalance as DeductionBalanceCb;
use Application_Model_Grid_Callback_MilesLE as MilesLECb;
use Application_Model_Grid_Callback_NegativeMoney as NegativeMoneyCb;
use Application_Model_Grid_Callback_Num as NumCb;
use Application_Model_Grid_Callback_Quantity as QuantityCb;
use Application_Model_Grid_Callback_ReserveAccountBalance as ReserveAccountBalanceCb;
use Application_Model_Grid_Callback_SettlementReserveTransactionQuickEditAmount as ReserveTransactionAmountQECb;
use Application_Model_Grid_Callback_Taxable as TaxableCb;

class Application_Model_Report_Statement extends Application_Model_Report_Reporting
{
    protected $view = '/reporting/grid/contractor-settlement-statement.phtml';
    public static $title = 'Contractor Settlement Statement';

    public function getGridData()
    {
        $fieldNames = User::getCurrentUser()->getSelectedCarrier()->getCustomFieldNames();
        $fieldNames->reporting = true;
        $totalData = ['contractors' => []];
        $cycleId = $this->getCycleIdForFilter()[0];
        $cycle = (new SettlementCycle())
            ->load($cycleId)
            ->changeDateFormat(
                ['disbursement_date', 'processing_date', 'cycle_start_date', 'cycle_close_date'],
                true,
                true
            );
        //        $cycle->saveHistory();
        $this->loadHistory($cycle);
        //        $accountHistory = (new Application_Model_Entity_Accounts_Reserve_History())
        //            ->getCollection()
        //            ->addFilter('settlement_cycle_id', $cycleId)
        //            ->setOrder('reserve_account_id')
        //            ->getItems('reserve_account_id');
        $carrier = new Carrier();
        if ($cycle->getCarrierId()) {
            $carrier->load($cycle->getCarrierId(), 'entity_id');
        }
        $this->addDataFromHistoryCollection($carrier, ['entity_id' => ['name', 'address']]);
        $rawContractors = $cycle->getSettlementContractors();
        $contractorsWithSettlements = [];
        $contractors = $this->getContractorId(['order' => ['company_name' => 'ASC']]);
        foreach ($rawContractors as $contractor) {
            $contractorsWithSettlements[$contractor['id']] = $contractor;
        }

        foreach ($contractors as $contractorId) {
            if (!isset($contractorsWithSettlements[$contractorId])) {
                continue;
            }
            $contractor = (new Contractor())->load($contractorId, 'entity_id');
            $this->addDataFromHistoryCollection(
                $contractor,
                ['entity_id' => true]
            );
            $contractor->decrypt();
            $data = [
                'info' => [
                    'contractor' => $contractor,
                    'carrier' => $carrier,
                    'cycle' => $cycle,
                ],
                'payment-grid' => [
                    'fields' => [
                        'shipment_complete_date' => [
                            'title' => $fieldNames->getShipmentCompleteDate() ?? 'Shipment Complete Date',
                        ],
                        'driver' => [
                            'title' => $fieldNames->getDriver() ?? 'Driver',
                        ],
                        'reference' => [
                            'title' => $fieldNames->getReference() ?? 'Reference',
                        ],
                        'taxable' => [
                            'title' => $fieldNames->getTaxable() ?? 'Taxable',
                        ],
                        'loaded_miles' => [
                            'title' => $fieldNames->getLoadedMiles() ?? 'Loaded Miles',
                        ],
                        'empty_miles' => [
                            'title' => $fieldNames->getEmptyMiles() ?? 'Empty Miles',
                        ],
                        'miles_l_e' => [
                            'title' => $fieldNames->getMilesLE() ?? 'Miles L-E',
                        ],
                        'payment_code' => [
                            'title' => $fieldNames->getPaymentCode() ?? 'Payment Code',
                        ],
                        'powerunit_code' => [
                            'title' => $fieldNames->getPowerunitCode() ?? 'Power Unit',
                        ],
                        'destination_city' => [
                            'title' => $fieldNames->getPowerunitCode() ?? 'Origin Dest',
                        ],
                        'description' => [
                            'title' => $fieldNames->getDescription() ?? 'Description',
                        ],
                        'invoice_date' => [
                            'title' => $fieldNames->getInvoiceDate(),
                        ],
                        'quantity' => [
                            'title' => 'Qty',
                        ],
                        'rate' => [
                            'title' => 'Rate',
                        ],
                        'amount' => [
                            'title' => 'Compensation Amt',
                        ],
                    ],
                    'callbacks' => [
                        'quantity' => QuantityCb::class,
                        'rate' => BalanceCb::class,
                        'amount' => BalanceCb::class,
                        'shipment_complete_date' => DateFormatCb::class,
                        'taxable' => TaxableCb::class,
                        'loaded_miles' => NumCb::class,
                        'empty_miles' => NumCb::class,
                        'miles_l_e' => MilesLECb::class,
                    ],
                    'items' => [],
                    'total_quantity' => 0,
                    'total_amount' => 0,
                ],
                'deduction-grid' => [
                    'fields' => [
                        'deduction_code' => [
                            'title' => 'Code',
                        ],
                        'powerunit_code' => [
                            'title' => 'Power Unit',
                        ],
                        'reference' => [
                            'title' => 'Reference',
                        ],
                        'description' => [
                            'title' => 'Description',
                        ],
                        'created_datetime' => [
                            'title' => 'Transaction Date',
                        ],
                        'transaction_fee' => [
                            'title' => 'Transaction Fee',
                        ],
                        'deduction_amount' => [
                            'title' => 'Deduction Amount',
                        ],
                        'balance_due' => [
                            'title' => 'Balance Remaining',
                        ],
                    ],
                    'callbacks' => [
                        'created_datetime' => DateFormatCb::class,
                        'balance_due' => BalanceCb::class,
                        'transaction_fee' => NegativeMoneyCb::class,
                        'deduction_amount' => NegativeMoneyCb::class,
                    ],
                    'items' => [],
                    'total_deduction_amount' => 0,
                    'total_deduction_balance' => 0,
                ],
                'contribution-grid' => [
                    'fields' => [
                        'created_datetime' => [
                            'title' => 'Transaction Date',
                        ],
                        'account_name' => [
                            'title' => 'Reserve Account',
                        ],
                        'vendor_reserve_code' => [
                            'title' => 'Code',
                        ],
                        'description' => [
                            'title' => 'Description',
                        ],
                        'powerunit_code' => [
                            'title' => 'Power Unit',
                        ],
                        'reference' => [
                            'title' => 'Reference',
                        ],
                        'title' => [
                            'title' => 'Transaction Type',
                        ],
                        'balance' => [
                            'title' => 'Remaining Balance',
                        ],
                        'amount' => [
                            'title' => 'Amount',
                        ],
                    ],
                    'callbacks' => [
                        'created_datetime' => DateFormatCb::class,
                        'quantity' => QuantityCb::class,
                        'rate' => BalanceCb::class,
                        'amount' => ReserveTransactionAmountQECb::class,
                        'balance' => DeductionBalanceCb::class,
                        // 'deduction_amount' => DeductionBalanceCb::class,
                    ],
                    'items' => [],
                    'total_amount' => 0,
                ],
                'settlement-grid' => [
                    'total_net' => 0,
                    'total_refunded_reserves' => 0,
                ],
                'account-balances-grid' => [
                    'fields' => [
                        'vendor_name' => [
                            'size' => '13.2%',
                            'title' => 'Vendor',
                        ],
                        'account_name' => [
                            'size' => '12.4%',
                            'title' => 'Reserve Account',
                        ],
                        'vendor_reserve_code' => [
                            'size' => '11.1%',
                            'title' => 'Code',
                        ],
                        'account_description' => [
                            'title' => 'Description',
                            'size' => '7,5%',
                        ],
                        'starting_balance' => [
                            'size' => '11.1%',
                            'title' => 'Starting Balance',
                        ],
                        'adjustments' => [
                            'size' => '11.1%',
                            'title' => 'Adjustment',
                        ],
                        'withdrawals' => [
                            'size' => '11.1%',
                            'title' => 'Withdrawal',
                        ],
                        'contributions' => [
                            'size' => '11.1%',
                            'title' => 'Contribution',
                        ],
                        'ending_balance' => [
                            'size' => '11.1%',
                            'title' => 'Ending Balance',
                        ],
                    ],
                    'callbacks' => [
                        'starting_balance' => ReserveAccountBalanceCb::class,
                        'adjustments' => ReserveAccountBalanceCb::class,
                        'withdrawals' => ReserveAccountBalanceCb::class,
                        'contributions' => ReserveAccountBalanceCb::class,
                        'ending_balance' => ReserveAccountBalanceCb::class,
                    ],
                    'items' => [],
                    'total_starting_balance' => 0,
                    'total_withdrawals' => 0,
                    'total_adjustments' => 0,
                    'total_contributions' => 0,
                    'total_ending_balance' => 0,
                ],
            ];

            $payments = (new Payment())
                ->getCollection()
                ->addFilter('contractor_id', $contractorId)
                ->addFilter('settlement_cycle_id', $cycleId)
                ->setOrder('invoice_date', 'ASC')
                ->setOrder('description', 'ASC')
                ->setOrder('category', 'ASC')
                ->setOrder('department', 'ASC')
                ->addNonDeletedFilter()
                ->getItems();
            foreach ($payments as $payment) {
                $this->addDataFromHistoryCollection($payment, ['scarrier_id' => ['name' => 'scarrier_name']]);
                $payment->changeDateFormat(['invoice_date'], true, true);
                $data['payment-grid']['items'][] = $payment;
                $data['payment-grid']['total_quantity'] += $payment->getQuantity();
                $data['payment-grid']['total_amount'] += $payment->getAmount();
            }
            $deductions = (new Deduction())
                ->getCollection()
                ->addFilter('contractor_id', $contractorId)
                ->addFilter('settlement_cycle_id', $cycleId)
                ->setOrder('provider_name', 'ASC')
                ->setOrder('invoice_date', 'ASC')
                ->setOrder('description', 'ASC')
                ->setOrder('amount', 'DESC')
                ->addNonDeletedFilter()
                ->addWithdrawals()
                ->getItems();

            foreach ($deductions as $deduction) {
                $this->addDataFromHistoryCollection($deduction, ['provider_id' => ['name' => 'provider_name']]);
                $deduction->changeDateFormat(['invoice_date'], true, true);
                $deduction->setBalanceDue($deduction->getAmount() - $deduction->getDeductionAmount());
                //$deduction->setDeductionAmt($deduction->getAmount() - $deduction->getDeductionBalance());
                $data['deduction-grid']['items'][] = $deduction;
                $data['deduction-grid']['total_deduction_amount'] += $deduction->getDeductionAmount();
                $data['deduction-grid']['total_deduction_balance'] += $deduction->getBalanceDue();
            }

            $contributions = (new ReserveTransaction())
                ->getCollection()
                ->addNonDeletedFilter()
                ->addFilter('contractor_id', $contractorId)
                ->addFilter('settlement_cycle_id', $cycleId)
                ->addFilter(
                    'type',
                    [ ReserveTrxTypes::CONTRIBUTION, ReserveTrxTypes::WITHDRAWAL ],
                    'IN'
                )
                ->setOrder('created_datetime', 'ASC')
                ->getItems();
            foreach ($contributions as $contribution) {
                $this->addDataFromHistoryCollection($contribution, ['entity_id' => ['name' => 'vendor_name']]);
                $sign = ($contribution->getType() == ReserveTrxTypes::CONTRIBUTION) ? -1 : 1;
                $data['contribution-grid']['items'][] = $contribution;
                $data['contribution-grid']['total_amount'] += $sign * $contribution->getAmount();
            }

            $reserveAccounts = (new ReserveContractor())
                ->getContractorAccountBalances($cycleId, $contractorId);
            foreach ($reserveAccounts as $reserveAccount) {
                $reserveAccount['adjustments'] += $reserveAccount['a_increase'] - $reserveAccount['a_decrease'];
                $reserveAccount['starting_balance'] -= $reserveAccount['adjustments'];
                $reserveAccountEntity = (new ReserveContractor())->setData($reserveAccount);
                $this->addDataFromHistoryCollection($reserveAccountEntity, ['vendor_id' => ['name' => 'vendor_name']]);
                $data['account-balances-grid']['items'][] = $reserveAccountEntity;
                $data['account-balances-grid']['total_starting_balance'] += $reserveAccount['starting_balance'];
                $data['account-balances-grid']['total_adjustments'] += $reserveAccount['adjustments'];
                $data['account-balances-grid']['total_withdrawals'] += $reserveAccount['withdrawals'];
                $data['account-balances-grid']['total_contributions'] += $reserveAccount['contributions'];
                $data['account-balances-grid']['total_ending_balance'] += $reserveAccount['ending_balance'];
            }
            $data['settlement-grid']['total_refunded_reserves'] = $data['account-balances-grid']['total_withdrawals'];

            $data['settlement-grid']['total_net'] = $data['payment-grid']['total_amount'] - $data['deduction-grid']['total_deduction_amount'] - $data['contribution-grid']['total_amount'] + $data['account-balances-grid']['total_withdrawals'];

            $totalData['contractors'][$contractorId] = $data;
        }

        return $totalData;
    }
}
