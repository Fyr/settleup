<?php

class Application_Model_Report_Remittance extends Application_Model_Report_Reporting
{
    public static $title = 'Deduction Remittance File';
    protected $view = '/reporting/grid/deduction-remittance.phtml';

    public function getGridData()
    {
        $toFile = $this->isToFile();
        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load($this->getPeriod());
        $data = (new Application_Model_Report_Remittance())->create(
            $cycle,
            $carrierVendor = $this->getCarrierVendorId(),
            $toFile
        );
        if (!$toFile) {
            $data = ['data' => $data];
        }

        return $data;
    }

    public function create(Application_Model_Entity_Settlement_Cycle $cycle, $carrierVendorId, $toFile = true)
    {
        $report = [
            ['Remittance Advice'],
            [''],
            ['View By:', 'Settlement Cycle'],
            ['Period:', $cycle->getCyclePeriodString()],
            [''],
            [
                'ID',
                'Company',
                'Division',
                'Vendor',
                'Vendor Acct #',
                'Deduction Code',
                'Description',
                'Invoice #',
                'Invoice Date',
                //                'Invoice Due Date',
                'Settlement Cycle',
                'Quantity',
                'Rate',
                'Amount',
                'Balance',
                'Deduction Amount',
            ],
        ];
        $deductions = (new Application_Model_Entity_Deductions_Deduction())->getCollection()->addVendorAcctField(
        )->addFilter(
            'settlement_cycle_id',
            $cycle->getId()
        )->addFilter(
            'provider_id',
            $carrierVendorId,
            'IN'
        )->addNonDeletedFilter()->setOrder('company_name', 'ASC')->setOrder('invoice_date', 'ASC')->setOrder(
            'invoice_id',
            'ASC'
        );

        if ($deductions->count()) {
            $totals = [
                'quantity' => 0,
                'rate' => 0,
                'amount' => 0,
                'balance' => 0,
                'deduction_amount' => 0,
            ];
            foreach ($deductions as $deduction) {
                $deduction->changeBalanceForReport();
                $deduction->getDeductionAmount();
                foreach ($totals as $field => $value) {
                    $totals[$field] += (float)$deduction->getData($field);
                }
                $deduction->changeDateFormat(['invoice_date', 'invoice_due_date'], true);
                $balance = $deduction->getBalanceForRemittanceReport();
                $data = [
                    $deduction->getContractorCode(),
                    $deduction->getCompanyName(),
                    $deduction->getDivision(),
                    $deduction->getProviderName(),
                    $deduction->getVendorAcct(),
                    $deduction->getDeductionCode(),
                    $deduction->getDescription(),
                    $deduction->getInvoiceId(),
                    $deduction->getInvoiceDate(),
                    $cycle->getCyclePeriodString(),
                    ((float)$deduction->getQuantity()) ? number_format((float)$deduction->getQuantity(), 1) : '-',
                    ((float)$deduction->getRate()) ? '$' . number_format((float)$deduction->getRate(), 2) : '-',
                    ((float)$deduction->getAmount()) ? '$' . number_format((float)$deduction->getAmount(), 2) : '-',
                    (!is_null($balance)) ? '$' . number_format((float)$balance, 2) : '-',
                    ((float)$deduction->getDeductionAmount()) ? '$' . number_format(
                        (float)$deduction->getDeductionAmount(),
                        2
                    ) : '-',
                ];
                $data = array_map(
                    fn ($value) => $value ?? '',
                    $data
                );
                $report[] = $data;
            }
            $report[] = [''];
            $report[] = [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'Total Deduction:',
                number_format($totals['quantity'], 1),
                '',
                '$' . number_format($totals['amount'], 2),
                '$' . number_format($totals['balance'], 2),
                '$' . number_format($totals['deduction_amount'], 2),
            ];
        } else {
            $report[] = ['None!'];
        }

        if ($toFile) {
            return $this->_saveToFile($report, $cycle->getId());
        } else {
            return $this->_saveToFile($report, $cycle->getId(), false);
        }
    }

    /**
     * @param null $checkId
     * @param bool $toFile
     * @return string
     */
    private function _saveToFile(array $data, $checkId = null, $toFile = true)
    {
        $content = '';
        foreach ($data as $line) {
            foreach ($line as $value) {
                if ($value == null) {
                    $value = '';
                }
                $content .= '"' . $value . '",';
            }
            $content = substr($content, 0, strlen($content) - 1);
            $content .= "\n";
        }

        $content = substr($content, 0, strlen($content) - 1);

        if ($toFile) {
            $fileTitle = 'remittance-' . $checkId . '.csv';
            file_put_contents(
                Application_Model_File::getStorage() . '/' . $fileTitle,
                $content
            );

            return $fileTitle;
        } else {
            return $content;
        }
    }
}
