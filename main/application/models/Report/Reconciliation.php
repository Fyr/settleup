<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_CustomFieldNames as CustomFieldNames;
use Application_Model_Entity_Entity_Carrier as Carrier;
use Application_Model_Entity_Settlement_Cycle as Cycle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Application_Model_Report_Reconciliation extends Application_Model_Report_Reporting
{
    protected $view = '/reporting/grid/settlement-reconciliation.phtml';
    public static $title = 'Settlement Reconciliation';
    /** @var CustomFieldNames */
    protected $fieldNames;
    /** @var Cycle */
    protected $cycle;
    /** @var Carrier */
    protected $carrier;

    protected function init()
    {
        $this->fieldNames = User::getCurrentUser()->getSelectedCarrier()->getCustomFieldNames();
        $cycleId = $this->getCycleIdForFilter()[0];
        $this->cycle = Cycle::staticLoad($cycleId)->changeDateFormat([
            'disbursement_date',
            'cycle_close_date',
            'cycle_start_date',
        ], true, true);
        $this->carrier = $this->cycle->getCarrier();
    }

    public function getGridData()
    {
        $this->addDataFromHistoryCollection($this->carrier, ['entity_id' => ['name', 'address']]);
        $data = [
            'info' => [
                'carrier' => $this->carrier,
                'cycle' => $this->cycle,
            ],
            'payment-grid' => $this->getPaymentGridData(),
            'deduction-grid' => $this->getDeductionGridData(),
            'transaction-grid' => $this->getTransactionGridData(),
        ];

        $payments = [];
        foreach ($this->getPaymentCollection() as $payment) {
            if (isset($payments[$payment->getContractorId()])) {
                $payments[$payment->getContractorId()]->setQuantity(
                    $payments[$payment->getContractorId()]->getQuantity() + $payment->getQuantity()
                )->setAmount($payments[$payment->getContractorId()]->getAmount() + $payment->getAmount());
            } else {
                $payments[$payment->getContractorId()] = $payment;
            }
            $data['payment-grid']['total_quantity'] += $payment->getQuantity();
            $data['payment-grid']['total_amount'] += $payment->getAmount();
        }
        $data['payment-grid']['items'] = $payments;

        $deductions = [];
        foreach ($this->getDeductionCollection() as $deduction) {
            if (isset($deductions[$deduction->getProviderId()])) {
                $deductions[$deduction->getProviderId()]->setQuantity(
                    $deductions[$deduction->getProviderId()]->getQuantity() + $deduction->getQuantity()
                )->setAmount(
                    $deductions[$deduction->getProviderId()]->getAmount() + $deduction->getAmount()
                )->setDeductionBalance(
                    $deductions[$deduction->getProviderId()]->getDeductionBalance(
                    ) + $deduction->getDeductionBalance()
                )->setDeductionAmount(
                    $deductions[$deduction->getProviderId()]->getDeductionAmount() + $deduction->getDeductionAmount(
                    )
                );
            } else {
                $deductions[$deduction->getProviderId()] = $deduction;
            }
            $data['deduction-grid']['total_quantity'] += $deduction->getQuantity();
            $data['deduction-grid']['total_amount'] += $deduction->getAmount();
            $data['deduction-grid']['total_deduction_balance'] += $deduction->getDeductionBalance();
            $data['deduction-grid']['total_deduction_amount'] += $deduction->getDeductionAmount();
        }
        $data['deduction-grid']['items'] = $deductions;

        foreach ($this->getReserveAccounts() as $reserveAccount) {
            $reserveAccountEntity = (new Application_Model_Entity_Accounts_Reserve_Powerunit())->setData(
                $reserveAccount
            );
            $data['transaction-grid']['items'][] = $reserveAccountEntity;
            $data['transaction-grid']['total_starting_balance'] += $reserveAccount['starting_balance'];
            $data['transaction-grid']['total_withdrawals'] += $reserveAccount['withdrawals'];
            $data['transaction-grid']['total_contributions'] += $reserveAccount['contributions'];
            $data['transaction-grid']['total_ending_balance'] += $reserveAccount['ending_balance'];
        }
        $data['totals'] = [
            'contractor' => [
                'payments' => $data['payment-grid']['total_amount'],
                'deductions' => $data['deduction-grid']['total_deduction_amount'],
                'contributions' => $data['transaction-grid']['total_contributions'],
                'withdrawals' => $data['transaction-grid']['total_withdrawals'],
                'total' => $data['payment-grid']['total_amount'] - $data['deduction-grid']['total_deduction_amount'] - $data['transaction-grid']['total_contributions'] + $data['transaction-grid']['total_withdrawals'],
            ],
            'vendor' => [
                'deductions' => $data['deduction-grid']['total_deduction_amount'],
                'contributions' => $data['transaction-grid']['total_contributions'],
                'withdrawals' => $data['transaction-grid']['total_withdrawals'],
                'total' => $data['deduction-grid']['total_deduction_amount'] + $data['transaction-grid']['total_contributions'] - $data['transaction-grid']['total_withdrawals'],
            ],
        ];
        $data['totals']['grand'] = [
            'contractor' => $data['totals']['contractor']['total'],
            'vendor' => $data['totals']['vendor']['total'],
            'total' => $data['totals']['contractor']['total'] + $data['totals']['vendor']['total'],
        ];

        if ($this->getAction() == Application_Model_Report_Reporting::DOWNLOAD_ACTION && in_array(
            $this->getFileType(),
            [Application_Model_File_Type_Xls::XLS_TYPE, Application_Model_File_Type_Xls::XLSX_TYPE]
        )) {
            $this->saveExcelPaymentData($data);
        }

        return $data;
    }

    public function saveExcelPaymentData($data)
    {
        $excelData = [];

        $excelData[] = [
            [
                'value' => $this->getReportTitle(),
                'style' => ['font' => ['bold' => true, 'name' => 'Arial', 'size' => 12, 'italic' => true]],
            ],
        ];

        $excelData[] = [];
        $addresses = $data['info']['carrier']->getAddress();
        $multipleCarrierAddress = ((is_countable($addresses) ? count($addresses) : 0) > 1);
        $addressString = '';
        $cityStateZipString = '';
        foreach ($addresses as $itemNumber => $address) {
            $addressString .= ($address['address'] . (($multipleCarrierAddress) ? ' / ' : ''));
            $cityStateZipString .= ($address['city'] . ', ' . $address['state'] . ' ' . $address['zip'] . (($multipleCarrierAddress && $itemNumber != (is_countable($addresses) ? count(
                $addresses
            ) : 0) - 1) ? ' / ' : ''));
        }

        $header = [
            [
                [
                    'value' => 'Division:',
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => $data['info']['carrier']->getName(),
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                    ],
                ],
                [
                    'value' => 'Period Start Date:',
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => $data['info']['cycle']->getCycleStartDate(),
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                    ],
                ],
            ],
            [
                [
                    'value' => 'Address:',
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => $addressString,
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                    ],
                ],
                [
                    'value' => 'Period Close Date:',
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => $data['info']['cycle']->getCycleCloseDate(),
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                    ],
                ],
            ],
            [
                [
                    'value' => 'City, State, Zip:',
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => $cityStateZipString,
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                    ],
                ],
                [
                    'value' => 'Disbursement Date:',
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => $data['info']['cycle']->getDisbursementDate(),
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                    ],
                ],
            ],

        ];

        $excelData = [...$excelData, ...$header];

        $excelData[] = [];

        $excelData[] = [
            [
                'value' => 'Compensations',
                'style' => ['font' => ['bold' => true, 'name' => 'Arial', 'size' => 12, 'italic' => true]],
            ],
        ];

        $excelData[] = [];

        $data['hideCycleHeader'] = true;
        $data['hideTitle'] = true;

        $data['fields'] = [
            'scarrier_name' => 'Division',
            'contractor_code' => 'Contractor ID',
            'company_name' => 'Contractor',
            'division' => 'Division',
            'quantity' => 'Quantity',
            'amount' => 'Compensation Total',
        ];

        $data['callbacks'] = $data['payment-grid']['callbacks'];

        $data['excelStyle'] = [
            'quantity' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                'numberformat' => ['code' => '0.0'],
            ],
            'amount' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
            ],
            'deduction_balance' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
            ],
            'starting_balance' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
            ],
            'ending_balance' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
            ],
            'contributions' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
            ],
            'withdrawals' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
            ],
            'deduction_amount' => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
            ],
            'division' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
            'contractor_code' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
            'scarrier_name' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
            'provider_name' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
            'company_name' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
            'vendor_reserve_code' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
            'vendor_name' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
            'account_name' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
            'description' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
        ];

        $data['contractors'][0]['payments'] = $data['payment-grid']['items'];

        $total = [
            ['value' => null],
            ['value' => null],
            ['value' => null],
            [
                'value' => 'Total Compensations:',
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ],
            ],
            [
                'value' => $data['payment-grid']['total_quantity'],
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '0.0'],
                ],
            ],
            [
                'value' => $data['payment-grid']['total_amount'],
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
            ],
        ];

        $excelData = array_merge($excelData, $this->getExcelData($data, 'payments', $total));

        $excelData[] = [];
        $excelData[] = [];
        $excelData[] = [
            [
                'value' => 'Deductions',
                'style' => ['font' => ['bold' => true, 'name' => 'Arial', 'size' => 12, 'italic' => true]],
            ],
        ];

        $excelData[] = [];

        $data['fields'] = [
            'provider_name' => 'Vendor',
            'quantity' => 'Quantity',
            'amount' => 'Amount',
            'deduction_balance' => 'Balance',
            'deduction_amount' => 'Deduction Amount',
        ];

        $data['callbacks'] = $data['deduction-grid']['callbacks'];
        $data['contractors'][0]['deductions'] = $data['deduction-grid']['items'];

        $total = [
            [
                'value' => 'Total Deductions:',
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ],
            ],
            [
                'value' => $data['deduction-grid']['total_quantity'],
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '0.0'],
                ],
            ],
            [
                'value' => $data['deduction-grid']['total_amount'],
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
            ],
            [
                'value' => $data['deduction-grid']['total_deduction_balance'],
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
            ],
            [
                'value' => $data['deduction-grid']['total_deduction_amount'],
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
            ],

        ];

        $excelData = array_merge($excelData, $this->getExcelData($data, 'deductions', $total));

        $excelData[] = [];
        $excelData[] = [];
        $excelData[] = [
            [
                'value' => 'Reserve Transactions',
                'style' => ['font' => ['bold' => true, 'name' => 'Arial', 'size' => 12, 'italic' => true]],
            ],
        ];
        $excelData[] = [];

        $data['fields'] = [
            'vendor_name' => 'Vendor',
            'account_name' => 'Reserve Account',
            'vendor_reserve_code' => 'Reserve Code',
            'description' => 'Description',
            'starting_balance' => 'Starting Balance',
            'withdrawals' => 'Withdrawals',
            'contributions' => 'Contributions',
            'ending_balance' => 'Ending Balance',
        ];

        $data['contractors'][0]['transactions'] = $data['transaction-grid']['items'];
        $data['callbacks'] = $data['transaction-grid']['callbacks'];

        $total = [
            ['value' => null],
            ['value' => null],
            ['value' => null],
            [
                'value' => 'Total:',
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ],
            ],
            [
                'value' => $data['transaction-grid']['total_starting_balance'],
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
            ],
            [
                'value' => $data['transaction-grid']['total_withdrawals'],
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
            ],
            [
                'value' => $data['transaction-grid']['total_contributions'],
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
            ],
            [
                'value' => $data['transaction-grid']['total_ending_balance'],
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
            ],
        ];

        $excelData = array_merge($excelData, $this->getExcelData($data, 'transactions', $total));

        $excelData[] = [];

        $footer = [
            [
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                [
                    'value' => 'Total Compensations:',
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => $data['totals']['contractor']['payments'],
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                    ],
                ],
            ],
            [
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                [
                    'value' => 'Total Deductions:',
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => $data['totals']['contractor']['deductions'],
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                    ],
                ],
            ],
            [
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                [
                    'value' => 'Total Contributions:',
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => $data['totals']['contractor']['contributions'],
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                    ],
                ],
            ],
            [
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                [
                    'value' => 'Total Withdrawals:',
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => $data['totals']['contractor']['withdrawals'],
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                    ],
                ],
            ],
            [
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                [
                    'value' => 'Total Contractor Settlement:',
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => $data['totals']['contractor']['total'],
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                    ],
                ],
            ],
            [],
            [
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                [
                    'value' => 'Total Deductions:',
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => $data['totals']['vendor']['deductions'],
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                    ],
                ],
            ],
            [
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                [
                    'value' => 'Total Contributions:',
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => $data['totals']['vendor']['contributions'],
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                    ],
                ],
            ],
            [
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                [
                    'value' => 'Total Withdrawals:',
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => $data['totals']['vendor']['withdrawals'],
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                    ],
                ],
            ],
            [
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                [
                    'value' => 'Total Vendor Disbursement:',
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => $data['totals']['vendor']['total'],
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                    ],
                ],
            ],
            [],
            [
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                [
                    'value' => 'Total Contractor Settlement:',
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => $data['totals']['grand']['contractor'],
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                    ],
                ],
            ],
            [
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                [
                    'value' => 'Total Vendor Disbursement:',
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => $data['totals']['grand']['vendor'],
                    'style' => [
                        'font' => ['size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                    ],
                ],
            ],
            [
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                [
                    'value' => 'Grand Total Disbursement:',
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => $data['totals']['grand']['total'],
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                    ],
                ],
            ],
        ];

        $excelData = array_merge($excelData, $footer);

        $model = new Application_Model_File_Type_Xls($this->getFileName());
        if (isset($data['hideTitle'])) {
            $model->disableTitle = true;
        }
        $model->getFileFromArray($excelData);

        return $this;
    }

    protected function getPaymentGridData()
    {
        return [
            'fields' => [
                'scarrier_name' => [
                    'title' => 'Division',
                    'size' => '13.1%',
                ],
                'contractor_code' => [
                    'title' => 'Contractor ID',
                    'size' => '6.5%',
                ],
                'company_name' => [
                    'title' => 'Contractor',
                    'size' => '6.5%',
                ],
                'division' => [
                    'title' => 'Division',
                    'size' => '6.5%',
                ],
                'empty_field_2' => [
                    'title' => '',
                    'size' => '9.2%',
                ],
                'empty_field_1' => [
                    'title' => '',
                ],
                'quantity' => [
                    'title' => 'Quantity',
                    'size' => '6.7%',
                ],
                'amount' => [
                    'title' => 'Compensation Total',
                    'size' => '12.8%',
                ],
            ],
            'callbacks' => [
                'quantity' => Application_Model_Grid_Callback_Quantity::class,
                'amount' => Application_Model_Grid_Callback_Balance::class,
                'empty_field_1' => Application_Model_Grid_Callback_EmptyField::class,
                'empty_field_2' => Application_Model_Grid_Callback_EmptyField::class,
            ],
            'items' => [],
            'total_quantity' => 0,
            'total_amount' => 0,
        ];
    }

    protected function getDeductionGridData()
    {
        return [
            'fields' => [
                'provider_name' => [
                    'title' => 'Vendor',
                ],
                'empty_field_1' => [
                    'title' => '',
                ],
                'empty_field_2' => [
                    'title' => '',
                    'colspan' => 2,
                ],
                'quantity' => [
                    'title' => 'Quantity',
                ],
                'amount' => [
                    'title' => 'Amount',
                ],
                'deduction_balance' => [
                    'title' => 'Balance',
                ],
                'deduction_amount' => [
                    'title' => 'Deduction Amount',
                ],
            ],
            'callbacks' => [
                'quantity' => Application_Model_Grid_Callback_Quantity::class,
                'amount' => Application_Model_Grid_Callback_Balance::class,
                'deduction_balance' => Application_Model_Grid_Callback_Balance::class,
                'deduction_amount' => Application_Model_Grid_Callback_Balance::class,
            ],
            'items' => [],
            'total_quantity' => 0,
            'total_amount' => 0,
            'total_deduction_balance' => 0,
            'total_deduction_amount' => 0,
        ];
    }

    protected function getTransactionGridData()
    {
        return [
            'fields' => [
                'vendor_name' => [
                    'size' => '13.1%',
                    'title' => 'Vendor',
                ],
                'account_name' => [
                    'size' => '6.5%',
                    'title' => 'Reserve Account',
                ],
                'vendor_reserve_code' => [
                    'size' => '6.5%',
                    'title' => 'Reserve Code',
                ],
                'description' => [
                    'size' => '18.3%',
                    'title' => 'Description',
                ],
                'starting_balance' => [
                    'size' => '13.8%',
                    'title' => 'Starting Balance',
                ],
                'withdrawals' => [
                    'size' => '13.8%',
                    'title' => 'Withdrawals',
                ],
                'contributions' => [
                    'size' => '13.8%',
                    'title' => 'Contributions',
                ],
                'ending_balance' => [
                    'size' => '13.9%',
                    'title' => 'Ending Balance',
                ],
            ],
            'callbacks' => [
                'starting_balance' => Application_Model_Grid_Callback_Balance::class,
                'withdrawals' => Application_Model_Grid_Callback_Balance::class,
                'contributions' => Application_Model_Grid_Callback_Balance::class,
                'ending_balance' => Application_Model_Grid_Callback_Balance::class,
            ],
            'items' => [],
            'total_starting_balance' => 0,
            'total_withdrawals' => 0,
            'total_contributions' => 0,
            'total_ending_balance' => 0,
        ];
    }

    /**
     * @return Application_Model_Entity_Collection_Payments_Payment
     */
    protected function getPaymentCollection()
    {
        /** @var Application_Model_Entity_Collection_Payments_Payment $collection */
        $collection = (new Application_Model_Entity_Payments_Payment())->getCollection()->addFilter(
            'payments.carrier_id',
            $this->carrier->getEntityId()
        )->addFilter('payments.settlement_cycle_id', $this->cycle->getId())->setOrder(
            'company_name',
            'ASC'
        )->addNonDeletedFilter();

        return $collection;
    }

    /**
     * @return Application_Model_Entity_Collection_Deductions_Deduction
     */
    protected function getDeductionCollection()
    {
        $collection = (new Application_Model_Entity_Deductions_Deduction())->getCollection()->addFilter(
            'deductions.settlement_cycle_id',
            $this->cycle->getId()
        )->setOrder('deductions.created_datetime', 'ASC')->addNonDeletedFilter()->addWithdrawals();

        return $collection;
    }

    /**
     * @return array
     */
    protected function getReserveAccounts()
    {
        $reserveAccounts = $this->cycle->getCarrierAccountBalances();
        if (!is_array($reserveAccounts)) {
            $reserveAccounts = [];
        }

        return $reserveAccounts;
    }
}
