<?php

use Application_Model_Entity_System_ReserveTransactionTypes as ReserveTransactionTypes;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class Application_Model_Report_ReserveAccountHistory extends Application_Model_Report_Reporting
{
    protected $view = '/reporting/grid/reserve-account-vendor-history.phtml';
    public static $title = 'Reserve Account History';
    public $dateFilterOptions = [
        self::SETTLEMENT_CYCLES => 'Settlement Cycle',
    ];

    public function getGridData()
    {
        $accountIds = (new Application_Model_Entity_Accounts_Reserve_Powerunit())->getCollection()->addFilter(
            'entity_id',
            $this->getContractorId(),
            'IN'
        )->addFilter('vendor_reserve_account_id', $this->getReserveAccountId(), 'IN')->setOrder(
            'company_name',
            'ASC'
        )->getField('id');
        if (!$accountIds) {
            $accountIds = [];
        }

        return $this->getReserveAccountHistoryData($accountIds);
    }

    public function getReserveAccountHistoryData($accountIds)
    {
        $cycleIds = $this->getCycleIdForFilter();
        $contractorIds = $this->getContractorId();
        if (empty($contractorIds)) {
            $contractorIds = [0];
        }

        $data = [
            'fields' => [
                'contractor_code' => 'ID',
                'company_name' => 'Company',
                'division' => 'Division',
                'vendor_acct' => 'Vendor Acct #',
                'vendor_name' => 'Vendor',
                'account_name' => 'Reserve Account',
                'vendor_reserve_code' => 'Code',
                'cycle_period_string' => 'Settlement Cycle',
                'starting_balance' => 'Starting Balance',
                'adjustments' => 'Adjustments',
                'contributions' => 'Contributions',
                'withdrawals' => 'Withdrawals',
                'ending_balance' => 'Ending Balance',
            ],
            'callbacks' => [
                'starting_balance' => Application_Model_Grid_Callback_DeductionBalance::class,
                'adjustments' => Application_Model_Grid_Callback_DeductionBalance::class,
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
                'adjustments' => [
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
            'grand_total_adjustments' => 0,
            'grand_total_ending_balance' => 0,
            'accounts' => [],
            'contractor_title' => ($this->getSelectContractor(
            ) == self::ALL_CONTRACTORS) ? 'All' : Application_Model_Entity_Entity_Contractor::staticLoad(
                $contractorIds[0],
                'entity_id'
            )->getCompanyName(),
        ];

        $isDateRangeReport = ($this->getDateFilterType() == self::DATE_RANGE);

        foreach ($accountIds as $accountId) {
            $data['accounts'][$accountId] = [
                'items' => [],
                'total_starting_balance' => 0,
                'total_adjustments' => 0,
                'total_withdrawals' => 0,
                'total_contributions' => 0,
                'total_ending_balance' => 0,
            ];

            $reserveTransactions = (new Application_Model_Entity_Accounts_Reserve_Transaction())->getCollection(
            )->addFilter('contractor_id', $contractorIds, 'IN')->addFilter(
                'settlement_cycle_id',
                $cycleIds,
                'IN'
            )->addFilter('reserve_account_contractor', $accountId)->addNonDeletedFilter()->setOrder(
                'settlement_cycle_id',
                Application_Model_Base_Collection::SORT_ORDER_ASC
            )->setOrder('id', Application_Model_Base_Collection::SORT_ORDER_ASC);
            $startingBalance = null;
            $previousTransaction = null;
            $excludeCycles = [];
            foreach ($reserveTransactions as $reserveTransaction) {
                $reserveTransaction->changeDateFormat(['cycle_start_date', 'cycle_close_date'], true, true);
                $excludeCycles[] = $reserveTransaction->getSettlementCycleId();
                if (!$reserveTransaction->getId()) {
                    if (is_null(
                        $startingBalance
                    ) || $isNextCycle = ($previousTransaction && $reserveTransaction->getSettlementCycleId(
                    ) != $previousTransaction->getSettlementCycleId())) {
                        $startingBalance = $reserveTransaction->getStartingBalance();
                    }
                    $previousTransaction = $reserveTransaction;
                    $reserveTransaction->setStartingBalance($startingBalance);
                    $reserveTransaction->setEndingBalance($startingBalance);
                } else {
                    if (is_null(
                        $startingBalance
                    ) || $isNextCycle = ($previousTransaction && $reserveTransaction->getSettlementCycleId(
                    ) != $previousTransaction->getSettlementCycleId())) {
                        $history = Application_Model_Entity_Accounts_Reserve_History::staticLoad(
                            [
                                'reserve_account_id' => $reserveTransaction->getReserveAccountContractor(),
                                'settlement_cycle_id' => $reserveTransaction->getSettlementCycleId(),
                            ]
                        );
                        $startingBalance = $history->getStartingBalance();
                        if ($adjustmentAmount = $this->getAdjustmentAmount(
                            $reserveTransactions,
                            $reserveTransaction->getSettlementCycleId()
                        )) {
                            $startingBalance -= $adjustmentAmount;
                        }
                    }
                    $previousTransaction = $reserveTransaction;
                    $reserveTransaction->setStartingBalance($startingBalance);
                    if ($reserveTransaction->getType() == ReserveTransactionTypes::CONTRIBUTION) {
                        $reserveTransaction->setEndingBalance($startingBalance + $reserveTransaction->getAmount());
                        $reserveTransaction->setContributions($reserveTransaction->getAmount());
                    } elseif ($reserveTransaction->getType() == ReserveTransactionTypes::WITHDRAWAL) {
                        $reserveTransaction->setEndingBalance($startingBalance - $reserveTransaction->getAmount());
                        $reserveTransaction->setWithdrawals($reserveTransaction->getAmount());
                    } elseif ($reserveTransaction->getType() == ReserveTransactionTypes::ADJUSTMENT_INCREASE) {
                        $reserveTransaction->setAdjustments($reserveTransaction->getAmount());
                        $reserveTransaction->setEndingBalance($startingBalance + $reserveTransaction->getAmount());
                    } elseif ($reserveTransaction->getType() == ReserveTransactionTypes::ADJUSTMENT_DECREASE) {
                        $reserveTransaction->setAdjustments(-1 * $reserveTransaction->getAmount());
                        $reserveTransaction->setEndingBalance($startingBalance - $reserveTransaction->getAmount());
                    }
                    $contractor = Application_Model_Entity_Entity_Contractor::staticLoad(
                        $reserveTransaction->getContractorId(),
                        'entity_id'
                    );
                    $contractorVendor = Application_Model_Entity_Entity_ContractorVendor::staticLoad([
                        'contractor_id' => $contractor->getEntityId(),
                        'vendor_id' => $reserveTransaction->getVendorEntityId(),
                    ]);
                    $reserveTransaction->setDivision($contractor->getDivision());
                    if ($contractorVendor->getId()) {
                        $reserveTransaction->setVendorAcct($contractorVendor->getVendorAcct());
                    }
                    $startingBalance = $reserveTransaction->getEndingBalance();
                }

                $data['accounts'][$accountId]['items'][] = $reserveTransaction;

                $data['accounts'][$accountId]['total_withdrawals'] += $reserveTransaction->getWithdrawals();
                $data['accounts'][$accountId]['total_contributions'] += $reserveTransaction->getContributions();
                $data['accounts'][$accountId]['total_adjustments'] += $reserveTransaction->getAdjustments();
            }
            if (!$data['accounts'][$accountId]['items'] || true) {
                $filteredCycles = $cycleIds;
                foreach ($excludeCycles as $cycleId) {
                    if (($index = array_search($cycleId, $filteredCycles)) > -1) {
                        unset($filteredCycles[$index]);
                    }
                }
                if (!$filteredCycles) {
                    $historyCollection = (new Application_Model_Entity_Accounts_Reserve_History())->getCollection(
                    )->getEmptyCollection();
                } else {
                    $historyCollection = (new Application_Model_Entity_Accounts_Reserve_History())->getCollection(
                    )->addFilter('reserve_account_history.reserve_account_id', $accountId)->addFilter(
                        'settlement_cycle_id',
                        $filteredCycles,
                        'IN'
                    )->addFilter('starting_balance', 0, '!=', true, 'AND', true)->addFilter(
                        'current_balance',
                        0,
                        '!=',
                        true,
                        'OR',
                        true
                    );
                }

                if (!true && !$data['accounts'][$accountId]['items']) {
                    /** @var Application_Model_Entity_Accounts_Reserve_History $historyItem */
                    foreach ($historyCollection as $historyItem) {
                        $historyItem->changeDateFormat(['cycle_start_date', 'cycle_close_date'], true, true);
                        $transaction = $historyItem->getTransactionForReport();
                        $data['accounts'][$accountId]['items'][] = $transaction;
                        $startingBalance = $transaction->getStartingBalance();
                    }
                } else {
                    $items = [];
                    /** @var Application_Model_Entity_Accounts_Reserve_History $historyItem */
                    foreach ($historyCollection as $historyItem) {
                        $historyItem->changeDateFormat(['cycle_start_date', 'cycle_close_date'], true, true);
                        $transaction = $historyItem->getTransactionForReport();
                        $items[] = $transaction;
                    }
                    $newItems = [...$data['accounts'][$accountId]['items'], ...$items];
                    usort($newItems, function ($a, $b) {
                        if ($a->getSettlementCycleId() > $b->getSettlementCycleId()) {
                            return 1;
                        } elseif ($a->getSettlementCycleId() < $b->getSettlementCycleId()) {
                            return -1;
                        } elseif ($a->getId() > $b->getId()) {
                            return 1;
                        } else {
                            return -1;
                        }
                    });
                    $data['accounts'][$accountId]['items'] = $newItems;
                }
            } else {
                $data['accounts'][$accountId]['total_starting_balance'] = $data['accounts'][$accountId]['items'][0]->getStartingBalance(
                );
            }
            if ($data['accounts'][$accountId]['items']) {
                $data['accounts'][$accountId]['total_starting_balance'] = $data['accounts'][$accountId]['items'][0]->getStartingBalance(
                );
                $data['accounts'][$accountId]['total_ending_balance'] = $data['accounts'][$accountId]['items'][count(
                    $data['accounts'][$accountId]['items']
                ) - 1]->getEndingBalance();
            }

            //grand totals
            $data['grand_total_starting_balance'] += $data['accounts'][$accountId]['total_starting_balance'];
            $data['grand_total_ending_balance'] += $data['accounts'][$accountId]['total_ending_balance'];
            $data['grand_total_withdrawals'] += $data['accounts'][$accountId]['total_withdrawals'];
            $data['grand_total_contributions'] += $data['accounts'][$accountId]['total_contributions'];
            $data['grand_total_adjustments'] += $data['accounts'][$accountId]['total_adjustments'];
        }

        $data['key'] = 'contractors';
        $data['title'] = 'Reserve Account History';

        if ($this->getAction() == Application_Model_Report_Reporting::DOWNLOAD_ACTION && in_array(
            $this->getFileType(),
            [Application_Model_File_Type_Xls::XLS_TYPE, Application_Model_File_Type_Xls::XLSX_TYPE]
        )) {
            $this->saveExcelRAVHistory($data);
        }

        return $data;
    }

    public function saveExcelRAVHistory($data)
    {
        $excelData = [
            [
                [
                    'value' => $this->getReportTitle(),
                    'style' => ['font' => ['bold' => true, 'name' => 'Arial', 'size' => 12, 'italic' => true]],
                ],
            ],
            [],
            [
                [
                    'value' => 'View By:',
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => static::getDateFilterTypeOptions()[$this->getData('date_filter_type')],
                    'style' => ['font' => ['size' => 10, 'name' => 'Arial']],
                ],
            ],
            [
                [
                    'value' => 'Period:',
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => $this->getReportPeriodValue(),
                    'style' => ['font' => ['size' => 10, 'name' => 'Arial']],
                ],
            ],
            [
                [
                    'value' => 'Contractors:',
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ],
                ],
                [
                    'value' => $data['contractor_title'],
                    'style' => ['font' => ['size' => 10, 'name' => 'Arial']],
                ],
            ],
            [],
        ];

        $header = [];
        foreach ($data['fields'] as $fiend => $title) {
            $cell = [
                'value' => $title,
                'style' => ['font' => ['bold' => true, 'size' => 10, 'name' => 'Arial']],
            ];
            if (isset($data['excelStyle'][$fiend])) {
                $cell['style'] = array_merge($cell['style'], $data['excelStyle'][$fiend]);
            }
            $header[] = $cell;
        }
        $excelData[] = $header;
        $itemsCount = 0;
        foreach ($data['accounts'] as $account) {
            if (!empty($account['items'])) {
                ++$itemsCount;
            }
            foreach ($account['items'] as $entity) {
                $row = [];
                foreach ($data['fields'] as $fiend => $title) {
                    $method = 'get' . Application_Model_Base_Object::uc_words($fiend, '');
                    if (isset($data['callbacks'][$fiend])) {
                        $callback = $data['callbacks'][$fiend];
                        $value = $callback::getInstance()->getExcelValue($entity, $method, $this);
                    } else {
                        $value = $entity->$method();
                    }
                    $cell = [
                        'value' => $value,
                        'style' => ['font' => ['size' => 10, 'name' => 'Arial']],
                    ];
                    if (isset($data['excelStyle'][$fiend])) {
                        $cell['style'] = array_merge($cell['style'], $data['excelStyle'][$fiend]);
                    }
                    $row[] = $cell;
                }
                $excelData[] = $row;
            }
            if (empty($account['items'])) {
                continue;
            }
            $excelData[] = [
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
                    'value' => $account['total_starting_balance'],
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                        'borders' => ['top' => ['style' => Border::BORDER_THIN]],
                    ],
                ],
                [
                    'value' => $account['total_adjustments'],
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                        'borders' => ['top' => ['style' => Border::BORDER_THIN]],
                    ],
                ],
                [
                    'value' => $account['total_contributions'],
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                        'borders' => ['top' => ['style' => Border::BORDER_THIN]],
                    ],
                ],
                [
                    'value' => $account['total_withdrawals'],
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                        'borders' => ['top' => ['style' => Border::BORDER_THIN]],
                    ],
                ],
                [
                    'value' => $account['total_ending_balance'],
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                        'borders' => ['top' => ['style' => Border::BORDER_THIN]],
                    ],
                ],
            ];
            $excelData[] = [];
        }

        if ($itemsCount > 1) {
            $excelData[] = [
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                ['value' => null],
                [
                    'value' => 'Grand Total:',
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
                        'borders' => [
                            'top' => ['style' => Border::BORDER_THIN],
                            'bottom' => ['style' => Border::BORDER_DOUBLE],
                        ],
                    ],
                ],
                [
                    'value' => $data['grand_total_adjustments'],
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                        'borders' => [
                            'top' => ['style' => Border::BORDER_THIN],
                            'bottom' => ['style' => Border::BORDER_DOUBLE],
                        ],
                    ],
                ],
                [
                    'value' => $data['grand_total_contributions'],
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                        'borders' => [
                            'top' => ['style' => Border::BORDER_THIN],
                            'bottom' => ['style' => Border::BORDER_DOUBLE],
                        ],
                    ],
                ],
                [
                    'value' => $data['grand_total_withdrawals'],
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                        'borders' => [
                            'top' => ['style' => Border::BORDER_THIN],
                            'bottom' => ['style' => Border::BORDER_DOUBLE],
                        ],
                    ],
                ],
                [
                    'value' => $data['grand_total_ending_balance'],
                    'style' => [
                        'font' => ['bold' => true, 'size' => 10, 'name' => 'Arial'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberformat' => ['code' => '"$"# ##0.00_);("$"# ##0.00)'],
                        'borders' => [
                            'top' => ['style' => Border::BORDER_THIN],
                            'bottom' => ['style' => Border::BORDER_DOUBLE],
                        ],
                    ],
                ],
            ];
        }

        (new Application_Model_File_Type_Xls($this->getFileName()))->getFileFromArray($excelData);

        return $this;
    }

    public function getAdjustmentAmount($transactions, $cycleId)
    {
        $amount = 0;
        foreach ($transactions as $transaction) {
            if ($transaction->getSettlementCycleId() == $cycleId) {
                if ($transaction->getType(
                ) == ReserveTransactionTypes::ADJUSTMENT_INCREASE) {
                    $amount += $transaction->getAmount();
                } elseif ($transaction->getType(
                ) == ReserveTransactionTypes::ADJUSTMENT_DECREASE) {
                    $amount -= $transaction->getAmount();
                }
            }
        }

        return $amount;
    }
}
