<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Application_Model_Report_ReserveAccountContractorHistory extends Application_Model_Report_Reporting
{
    public static $title = 'Reserve Account History';
    protected $view = '/reporting/grid/reserve-account-history.phtml';

    public function getGridData()
    {
        $accountIds = implode(',', $this->getReserveAccountContractorId());

        return $this->getReserveAccountContractorHistoryData($accountIds);
    }

    public $dateFilterOptions = [
        self::SETTLEMENT_CYCLES => 'Settlement Cycle',
    ];

    public function getReserveAccountContractorHistoryData($accountIds)
    {
        $data = [
            'fields' => [
                'contractor_code' => 'ID',
                'contractor_name' => 'Company',
                'division' => 'Division',
                'vendor_acct' => 'Vendor Acct #',
                'vendor_name' => 'Vendor',
                'vendor_reserve_code' => 'Code',
                'account_name' => 'Reserve Account',
                'cycle_period_string' => 'Settlement Cycle',
                'starting_balance' => 'Starting Balance',
                'withdrawals' => 'Withdrawals',
                'contributions' => 'Contributions',
                'ending_balance' => 'Ending Balance',
            ],
            'callbacks' => [
                'starting_balance' => Application_Model_Grid_Callback_DeductionBalance::class,
                'withdrawals' => Application_Model_Grid_Callback_DeductionBalance::class,
                'contributions' => Application_Model_Grid_Callback_DeductionBalance::class,
                'ending_balance' => Application_Model_Grid_Callback_DeductionBalance::class,
                'cycle_period_string' => Application_Model_Grid_Callback_ReportPeriod::class,
            ],
            'excelStyle' => [
                'contractor_code' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]],
                'starting_balance' => [
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
                'withdrawals' => [
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
                'contributions' => [
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
                'ending_balance' => [
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
            ],
            'grand_total_starting_balance' => 0,
            'grand_total_withdrawals' => 0,
            'grand_total_contributions' => 0,
            'grand_total_ending_balance' => 0,
            'reserve_accounts' => [],
        ];

        $cycleIds = implode(',', $this->getCycleIdForFilter());

        $reserveAccounts = (new Application_Model_Entity_Accounts_Reserve_Contractor())->getAccountsBalances(
            $cycleIds,
            $accountIds
        );
        foreach ($reserveAccounts as $reserveAccount) {
            $reserveAccountEntity = (new Application_Model_Entity_Accounts_Reserve_Contractor())->setData(
                $reserveAccount
            );
            $reserveAccountEntity->changeDateFormat(['cycle_start_date', 'cycle_close_date'], true, true);
            $data['reserve_accounts'][] = $reserveAccountEntity;
            $data['grand_total_starting_balance'] += $reserveAccount['starting_balance'];
            $data['grand_total_withdrawals'] += $reserveAccount['withdrawals'];
            $data['grand_total_contributions'] += $reserveAccount['contributions'];
            $data['grand_total_ending_balance'] += $reserveAccount['ending_balance'];
        }

        $data['key'] = 'reserve_accounts';
        $data['title'] = 'Reserve Account History';

        if ($this->getAction() == Application_Model_Report_Reporting::DOWNLOAD_ACTION && in_array(
            $this->getFileType(),
            [Application_Model_File_Type_Xls::XLS_TYPE, Application_Model_File_Type_Xls::XLSX_TYPE]
        )) {
            $this->saveExcelReserveAccountHistory($data);
        }

        return $data;
    }

    public function saveExcelReserveAccountHistory($data)
    {
        $total = [
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
                'value' => $data['grand_total_starting_balance'],
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
            ],
            [
                'value' => $data['grand_total_withdrawals'],
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
            ],
            [
                'value' => $data['grand_total_contributions'],
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
            ],
            [
                'value' => $data['grand_total_ending_balance'],
                'style' => [
                    'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                ],
            ],
        ];

        $data['contractors'] = [[$data['key'] => $data[$data['key']]]];
        $this->saveExcelReport($data, $data['key'], $total);

        return $this;
    }
}
