<?php

use Application_Model_Entity_Accounts_User as User;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Application_Model_Report_PaymentHistory extends Application_Model_Report_Reporting
{
    protected $view = '/reporting/grid/payment-history.phtml';
    public static $title = 'Compensation History';
    public $dateFilterOptions = [
        self::SETTLEMENT_CYCLES => 'Settlement Cycle',
        self::INVOICE_DATE => 'Invoice Date',
    ];

    public function getGridData()
    {
        $fieldNames = User::getCurrentUser()->getSelectedCarrier()->getCustomFieldNames();
        $data = [
            'fields' => [
                'contractor_code' => 'ID',
                'company_name' => 'Company',
                'division' => 'Division',
                'payment_code' => $fieldNames->getPaymentCode(),
                'description' => $fieldNames->getDescription(),
                'category' => $fieldNames->getCategory(),
                'department' => $fieldNames->getDepartment(),
                'invoice' => $fieldNames->getInvoice(),
                'invoice_date' => $fieldNames->getInvoiceDate(),
                'cycle_disbursement_date' => 'Disbursement Date',
                'cycle_period_string' => 'Settlement Cycle',
                'quantity' => 'Qty',
                'rate' => 'Rate',
                'amount' => 'Amt',
                'settlement_status_title' => 'Status',
            ],
            'callbacks' => [
                'quantity' => Application_Model_Grid_Callback_Quantity::class,
                'rate' => Application_Model_Grid_Callback_Balance::class,
                'amount' => Application_Model_Grid_Callback_Balance::class,
                'cycle_period_string' => Application_Model_Grid_Callback_ReportPeriod::class,
                'cycle_disbursement_date' => Application_Model_Grid_Callback_CycleDisbursementDate::class,
            ],
            'excelStyle' => [
                'quantity' => [
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '0.0'],
                ],
                'rate' => [
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
                'amount' => [
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
                'department' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
                'company_name' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
            ],
            'contractors' => [],
            'grand_total_quantity' => 0,
            'grand_total_amount' => 0,
        ];

        $contractorsId = $this->getContractorId();
        $paymentCollection = (new Application_Model_Entity_Payments_Payment())->getCollection()->addFilter(
            'contractor_id',
            $contractorsId,
            'IN'
        )->addFilter('settlement_cycle_id', $this->getCycleIdForFilter(), 'IN')->setOrder(
            'company_name',
            'ASC'
        )->setOrder('settlement_cycle_id', 'ASC')->setOrder('invoice_date', 'ASC')->setOrder(
            'description',
            'ASC'
        )->addNonDeletedFilter();

        if ($this->getDateFilterType() == Application_Model_Report_Reporting::INVOICE_DATE) {
            if (!$this->getInvoiceStartDate()) {
                $this->setInvoiceStartDate((new DateTime())->format('m/d/Y'));
            }
            if (!$this->getInvoiceEndDate()) {
                $this->setInvoiceEndDate((new DateTime())->format('m/d/Y'));
            }
            $paymentCollection->addFilter('invoice_date', $this->changeDateFormat($this->getInvoiceStartDate()), '>=');
            $paymentCollection->addFilter('invoice_date', $this->changeDateFormat($this->getInvoiceEndDate()), '<=');
        }
        $payments = $paymentCollection->getItems();
        foreach ($payments as $payment) {
            $contractorId = $payment->getContractorId();
            $payment->changeDateFormat(
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
            $data['contractors'][$contractorId]['payments'][] = $payment;
            if (!isset($data['contractors'][$contractorId]['total_quantity'])) {
                $data['contractors'][$contractorId]['total_quantity'] = 0;
            }
            if (!isset($data['contractors'][$contractorId]['total_amount'])) {
                $data['contractors'][$contractorId]['total_amount'] = 0;
            }
            $data['contractors'][$contractorId]['total_quantity'] += $payment->getQuantity();
            $data['contractors'][$contractorId]['total_amount'] += $payment->getAmount();
            $data['grand_total_quantity'] += $payment->getQuantity();
            $data['grand_total_amount'] += $payment->getAmount();
        }
        if ($this->getAction() == Application_Model_Report_Reporting::DOWNLOAD_ACTION && in_array(
            $this->getFileType(),
            [Application_Model_File_Type_Xls::XLS_TYPE, Application_Model_File_Type_Xls::XLSX_TYPE]
        )) {
            $data['fields'] = [
                'contractor_code' => 'ID',
                'company_name' => 'Company',
                'division' => 'Division',
                'payment_code' => $fieldNames->getPaymentCode(),
                'carrier_payment_code' => $fieldNames->getCarrierPaymentCode(),
                'description' => $fieldNames->getDescription(),
                'category' => $fieldNames->getCategory(),
                'department' => $fieldNames->getDepartment(),
                'gl_code' => $fieldNames->getGlCode(),
                'invoice' => $fieldNames->getInvoice(),
                'invoice_date' => $fieldNames->getInvoiceDate(),
                'disbursement_code' => $fieldNames->getDisbursementCode(),
                'cycle_disbursement_date' => 'Disbursement Date',
                'cycle_period_string' => 'Settlement Cycle',
                'quantity' => 'Qty',
                'rate' => 'Rate',
                'amount' => 'Amt',
                'settlement_status_title' => 'Status',
            ];
            $this->saveExcelPaymentHistory($data);
        }

        return $data;
    }

    public function saveExcelPaymentHistory($data)
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
        ];

        $this->saveExcelReport($data, 'payments', $total);

        return $this;
    }
}
