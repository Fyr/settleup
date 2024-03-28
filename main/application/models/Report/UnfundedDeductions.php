<?php

use Application_Model_Entity_Deductions_Deduction as Deduction;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Application_Model_Report_UnfundedDeductions extends Application_Model_Report_Reporting
{
    public static $title = 'Unfunded Deductions File';
    protected $view = '/reporting/grid/deduction-remittance.phtml';

    public function getGridData()
    {
        $data = [
            'fields' => [
                'contractor_code' => 'Contractor ID',
                'provider_code' => 'Vendor ID',
                'deduction_code' => 'Code',
                'description' => 'Description',
                'category' => 'Category',
                'department' => 'Department',
                'gl_code' => 'GL Code',
                'invoice_id' => 'Invoice',
                'invoice_date' => 'Invoice Date',
                'disbursement_code' => 'Disbursement Code',
                'cycle_disbursement_date' => 'Disbursement Date',
                'quantity' => 'Quantity',
                'balance' => 'Balance',
            ],
            'callbacks' => [
                'quantity' => Application_Model_Grid_Callback_Quantity::class,
                'balance' => Application_Model_Grid_Callback_DeductionReportBalance::class,
            ],
            'excelStyle' => [
                'quantity' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],],
                'balance' => [
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
                'department' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
                'contractor_code' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
                'provider_code' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
                'invoice_id' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
            ],
            'hideTitle' => true,
        ];

        /**
         * @var  $deductionCollection Application_Model_Entity_Collection_Deductions_Deduction
         */
        $deductionCollection = (new Deduction())->getCollection()->addSettlementFilter($this->getPeriod());

        $deductionCollection->addFilter('provider_id', $this->getCarrierVendorId(), 'IN')->addFilter(
            'IF(deductions.adjusted_balance IS NULL, deductions.balance, deductions.adjusted_balance) > 0',
            '',
            '',
            false
        )->addNonDeletedFilter()->setOrder('invoice_date', 'ASC');
        $deductions = $deductionCollection->getItems();

        foreach ($deductions as $id => $deduction) {
            $deductions[$id]->changeDateFormat(['invoice_date', 'cycle_disbursement_date'], true);
        }

        $data['hideCycleHeader'] = true;
        $data['contractors'] = [['deductions' => $deductionCollection->getItems()]];
        $this->saveExcelReport($data, 'deductions');
    }
}
