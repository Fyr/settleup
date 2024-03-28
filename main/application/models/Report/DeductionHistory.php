<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Application_Model_Report_DeductionHistory extends Application_Model_Report_Reporting
{
    protected $view = '/reporting/grid/deduction-history.phtml';
    public static $title = 'Deduction History';
    public $dateFilterOptions = [
        self::SETTLEMENT_CYCLES => 'Settlement Cycle',
        self::INVOICE_DATE => 'Invoice Date',
    ];

    public function getGridData()
    {
        $data = [
            'fields' => [
                'contractor_code' => 'ID',
                'company_name' => 'Contractor',
                'division' => 'Division',
                'provider_name' => 'Vendor',
                'deduction_code' => 'Code',
                'description' => 'Description',
                'category' => 'Category',
                'invoice_id' => 'Invoice',
                'invoice_date' => 'Inv Date',
                'cycle_disbursement_date' => 'Disbursement Date',
                'cycle_period_string' => 'Settlement Cycle',
                'quantity' => 'Qty',
                'rate' => 'Rate',
                'amount' => 'Amt',
                'balance' => 'Balance',
                'deduction_amount' => 'Deduction Amount',
                'settlement_status_title' => 'Status',
            ],
            'callbacks' => [
                'quantity' => Application_Model_Grid_Callback_Quantity::class,
                'rate' => Application_Model_Grid_Callback_Balance::class,
                'amount' => Application_Model_Grid_Callback_Balance::class,
                'balance' => Application_Model_Grid_Callback_DeductionReportBalance::class,
                'deduction_amount' => Application_Model_Grid_Callback_DeductionBalance::class,
                'cycle_period_string' => Application_Model_Grid_Callback_ReportPeriod::class,
                'cycle_disbursement_date' => Application_Model_Grid_Callback_CycleDisbursementDate::class,
            ],
            'excelStyle' => [
                'quantity' => [
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '0.00'],
                ],
                'rate' => [
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
                'amount' => [
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
                'balance' => [
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
                'deduction_amount' => [
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
                'department' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
                'company_name' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
            ],
            'contractors' => [],
            'grand_total_quantity' => 0,
            'grand_total_amount' => 0,
            'grand_total_balance' => 0,
            'grand_total_deduction_amount' => 0,
        ];

        $contractorsId = $this->getContractorId();
        $carrierVendorsId = $this->getCarrierVendorId();
        $deductionCollection = (new Application_Model_Entity_Deductions_Deduction())->getCollection()->addFilter(
            'contractor_id',
            $contractorsId,
            'IN'
        )->addfilter('provider_id', $carrierVendorsId, 'IN')->addFilter(
            'settlement_cycle_id',
            $this->getCycleIdForFilter(),
            'IN'
        )->setOrder('company_name', 'ASC')->setOrder('settlement_cycle_id', 'ASC')->setOrder(
            'provider_name',
            'ASC'
        )->setOrder(
            'invoice_date',
            'ASC'
            // )->setOrder('priority', 'ASC'
        )->addNonDeletedFilter();
        if ($this->getDateFilterType() == Application_Model_Report_Reporting::INVOICE_DATE) {
            if (!$this->getInvoiceStartDate()) {
                $this->setInvoiceStartDate((new DateTime())->format('m/d/Y'));
            }
            if (!$this->getInvoiceEndDate()) {
                $this->setInvoiceEndDate((new DateTime())->format('m/d/Y'));
            }
            $deductionCollection->addFilter(
                'invoice_date',
                $this->changeDateFormat($this->getInvoiceStartDate()),
                '>='
            );
            $deductionCollection->addFilter('invoice_date', $this->changeDateFormat($this->getInvoiceEndDate()), '<=');
        }

        $deductions = $deductionCollection->getItems();
        foreach ($deductions as $deduction) {
            $contractorId = $deduction->getContractorId();
            $deduction->changeDateFormat(
                [
                    'invoice_date',
                    'invoice_due_date',
                    'cycle_disbursement_date',
                    'disbursement_date',
                    'cycle_start_date',
                    'cycle_close_date',
                ],
                true,
                true
            );
            $data['contractors'][$contractorId]['deductions'][] = $deduction;
            if (!isset($data['contractors'][$contractorId]['total_quantity'])) {
                $data['contractors'][$contractorId]['total_quantity'] = 0;
            }
            if (!isset($data['contractors'][$contractorId]['total_amount'])) {
                $data['contractors'][$contractorId]['total_amount'] = 0;
            }
            if (!isset($data['contractors'][$contractorId]['total_balance'])) {
                $data['contractors'][$contractorId]['total_balance'] = 0;
            }
            if (!isset($data['contractors'][$contractorId]['total_deduction_amount'])) {
                $data['contractors'][$contractorId]['total_deduction_amount'] = 0;
            }
            $data['contractors'][$contractorId]['total_quantity'] += $deduction->getQuantity();
            $data['contractors'][$contractorId]['total_amount'] += $deduction->getAmount();
            $data['contractors'][$contractorId]['total_balance'] += $deduction->getDeductionBalance();
            $data['contractors'][$contractorId]['total_deduction_amount'] += $deduction->getDeductionAmount();
            $data['grand_total_quantity'] += $deduction->getQuantity();
            $data['grand_total_amount'] += $deduction->getAmount();
            $data['grand_total_balance'] += $deduction->getDeductionBalance();
            $data['grand_total_deduction_amount'] += $deduction->getDeductionAmount();
        }
        if ($this->getAction() == Application_Model_Report_Reporting::DOWNLOAD_ACTION && in_array(
            $this->getFileType(),
            [Application_Model_File_Type_Xls::XLS_TYPE, Application_Model_File_Type_Xls::XLSX_TYPE]
        )) {
            $data['fields'] = [
                'contractor_code' => 'ID',
                'company_name' => 'Contractor',
                'division' => 'Division',
                'provider_name' => 'Vendor',
                'deduction_code' => 'Code',
                'description' => 'Description',
                'category' => 'Category',
                'department' => 'Department',
                'gl_code' => 'GL Code',
                'invoice_id' => 'Invoice',
                'invoice_date' => 'Inv Date',
                'disbursement_code' => 'Disbursement Code',
                'cycle_disbursement_date' => 'Disbursement Date',
                'cycle_period_string' => 'Settlement Cycle',
                'quantity' => 'Qty',
                'rate' => 'Rate',
                'amount' => 'Amt',
                'balance' => 'Balance',
                'deduction_amount' => 'Deduction Amount',
                'settlement_status_title' => 'Status',
            ];
            $this->saveExcelDeductionHistory($data);
        }

        return $data;
    }

    public function saveExcelDeductionHistory($data)
    {
        $total = [
            ['value' => null],
            ['value' => null],
            ['value' => null],
            ['value' => null],
            ['value' => null],
            ['value' => null],
            ['value' => null],
            ['value' => null],
            ['value' => null],
            ['value' => null],
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
                'value' => $data['grand_total_quantity'],
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '0.00'],
                ],
            ],
            ['value' => null],
            [
                'value' => $data['grand_total_amount'],
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
            ],
            [
                'value' => $data['grand_total_balance'],
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
            ],
            [
                'value' => $data['grand_total_deduction_amount'],
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
            ],
        ];

        $this->saveExcelReport($data, 'deductions', $total);

        return $this;
    }
}
