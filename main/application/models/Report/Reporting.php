<?php

use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Application_Model_Report_Reporting extends Application_Model_Base_Entity
{
    use Application_Model_HistoryTrait;

    public const ON_SETTLEMENT_TYPE = 1;
    public const ON_DEMAND_TYPE = 2;
    /* Action types */
    public const VIEW_ACTION = 1;
    public const DOWNLOAD_ACTION = 2;
    /* Report types */
    public const ACH_FILE = 1;
    public const CHECK_FILE = 2;
    public const CONTRACTOR_SETTLEMENT_STATEMENT = 3;
    public const SETTLEMENT_RECONCILIATION = 4;
    public const PAYMENT_HISTORY = 5;
    public const DEDUCTION_HISTORY = 6;
    public const RESERVE_ACCOUNT_BALANCES = 7;
    public const RESERVE_ACCOUNT_HISTORY = 8;
    public const CONTRACTOR_1099 = 9;
    public const CONTRACTOR_STATUS = 10;
    public const DEDUCTION_REMITTANCE_FILE = 11;
    public const CONTRACTOR_EXPORT_FILE = 12;
    public const VENDOR_STATUS = 13;
    public const RESERVE_ACCOUNT_CONTRACTOR_HISTORY = 14;
    public const DISBURSEMENTS = 15;
    public const CHECK_PRINTING_FILE = 16;
    public const UNFUNDED_DEDUCTIONS = 17;
    /* Date filter type */
    public const SETTLEMENT_CYCLE = 1;
    public const DATE_RANGE = 2;
    public const SETTLEMENT_CYCLES = 3;
    public const INVOICE_DATE = 4;
    /* Select contractor types */
    public const ALL_CONTRACTORS = 1;
    public const SELECTED_CONTRACTORS = 2;
    public const MAIL_CONTRACTORS = 3;
    public const DISTRIBUTE_CONTRACTORS = 4;
    /* Select RA types */
    public const ALL_RA = 1;
    public const SELECTED_RA = 2;
    /* Select Carrier/Vendor types */
    public const ALL_CV = 1;
    public const SELECTED_CV = 2;
    public const ENTITY_CONTRACTOR = 1;
    public const ENTITY_CONTRACTOR_VENDOR = 2;
    public const ENTITY_VENDOR_CARRIER = 3;
    public $user;
    public $dateFilterOptions = [];
    protected $view = '/reporting/grid/grid.phtml';
    public static $title;
    protected $actionType;
    protected $prefix;

    public function getActionType()
    {
        return $this->actionType;
    }

    public function setActionType($type)
    {
        $this->actionType = $type;

        return $this;
    }

    public function isToFile()
    {
        return ($this->actionType == self::DOWNLOAD_ACTION);
    }

    public function getPrefix()
    {
        if (!$this->prefix) {
            $config = Zend_Controller_Front::getInstance()->getParam('bootstrap');
            if ($config->getOption('pdfAdapter') == Application_Model_File::MPDF_ADAPTER) {
                $prefix = 'm';
            } else {
                $prefix = 'wkhtmlto';
            }
            $this->prefix = $prefix;
        }

        return $this->prefix;
    }

    //-------Configuration-----------------------------------------------------------

    public static function getReportTypeOptions()
    {
        $data = [];
        foreach (self::getModels() as $id => $class) {
            $data[$id] = $class::$title;
        }

        return $data;
    }

    /**
     * return array of report classes
     *
     * @return array
     */
    public static function getModels()
    {
        return [
            self::DEDUCTION_REMITTANCE_FILE => Application_Model_Report_Remittance::class,
            self::CONTRACTOR_SETTLEMENT_STATEMENT => Application_Model_Report_Statement::class,
            self::SETTLEMENT_RECONCILIATION => Application_Model_Report_Reconciliation::class,
            self::PAYMENT_HISTORY => Application_Model_Report_PaymentHistory::class,
            self::DEDUCTION_HISTORY => Application_Model_Report_DeductionHistory::class,
            self::RESERVE_ACCOUNT_HISTORY => Application_Model_Report_ReserveAccountHistory::class,
            self::RESERVE_ACCOUNT_CONTRACTOR_HISTORY => Application_Model_Report_ReserveAccountPowerunitHistory::class,
            self::CONTRACTOR_STATUS => Application_Model_Report_ContractorStatus::class,
            self::VENDOR_STATUS => Application_Model_Report_VendorStatus::class,
            self::CONTRACTOR_EXPORT_FILE => Application_Model_Report_ContractorExport::class,
            self::UNFUNDED_DEDUCTIONS => Application_Model_Report_UnfundedDeductions::class,
        ];
    }

    /**
     * return instance of current report
     *
     * @return Application_Model_Report_Reporting | false
     */
    public function getModel()
    {
        if (isset(static::getModels()[$this->getType()])) {
            $class = static::getModels()[$this->getType()];
            $model = new $class();
            $model->setData($this->getData());
            $model->init();

            return $model;
        }

        return false;
    }

    protected function init()
    {
        return $this;
    }

    public function getTitle()
    {
        return self::$title;
    }

    public static function getDateFilterTypeOptions()
    {
        return [
            self::SETTLEMENT_CYCLE => 'Settlement Cycle',
            self::DATE_RANGE => 'Date Range',
            self::SETTLEMENT_CYCLES => 'Settlement Cycle',
            self::INVOICE_DATE => 'Invoice Date',
        ];
    }

    public static function getCheckEntityTypeOptions()
    {
        return [
            Application_Model_Entity_Entity_Type::TYPE_CONTRACTOR => 'Contractors',
            Application_Model_Entity_Entity_Type::TYPE_VENDOR => 'Vendors',
        ];
    }

    public static function getEntityTypeOptions()
    {
        return [
            self::ENTITY_CONTRACTOR => 'Contractors',
            self::ENTITY_CONTRACTOR_VENDOR => 'All Contractors and Vendors',
            self::ENTITY_VENDOR_CARRIER => 'Vendors',
        ];
    }

    public static function getSelectContractorOptions()
    {
        return [
            self::ALL_CONTRACTORS => 'All Contractors',
            self::MAIL_CONTRACTORS => 'Contractors - Mail',
            self::DISTRIBUTE_CONTRACTORS => 'Contractors - Distribute',
            self::SELECTED_CONTRACTORS => 'Select Contractor',

        ];
    }

    public static function getSelectRAOptions()
    {
        return [
            self::ALL_RA => 'All Reserve Accounts',
            self::SELECTED_RA => 'Select Reserve Account',
        ];
    }

    public static function getSelectCvOptions()
    {
        return [
            self::ALL_CV => 'All Vendors',
            self::SELECTED_CV => 'Select Vendor',
        ];
    }

    public static function getSelectContractorStatusOptions()
    {
        return [
            0 => 'All',
            Application_Model_Entity_System_ContractorStatus::STATUS_ACTIVE => 'Active',
            Application_Model_Entity_System_ContractorStatus::STATUS_TERMINATED => 'Terminated',
        ];
    }

    public static function getSelectVendorStatusOptions()
    {
        return [
            1 => 'All',
            Application_Model_Entity_System_VendorStatus::STATUS_ACTIVE => 'Approved',
            Application_Model_Entity_System_VendorStatus::STATUS_RESCINDED => 'Rescinded',
        ];
    }

    public function getReportPeriodValue()
    {
        if ($this->getData('date_filter_type') == Application_Model_Report_Reporting::DATE_RANGE) {
            $data = $this->getData('range_start_date') . ' - ' . $this->getData('range_end_date');
        } elseif ($this->getData('date_filter_type') == Application_Model_Report_Reporting::INVOICE_DATE) {
            $data = DateTime::createFromFormat('m/d/Y', $this->getData('invoice_start_date'))->format(
                'm/d/y'
            ) . ' - ' . DateTime::createFromFormat('m/d/Y', $this->getData('invoice_end_date'))->format('m/d/y');
        } elseif ($this->getData('date_filter_type') == Application_Model_Report_Reporting::SETTLEMENT_CYCLES) {
            $startCycle = Application_Model_Entity_Settlement_Cycle::staticLoad($this->getData('starting_period'));
            $startCycle->changeDateFormat(['cycle_start_date'], true, true);
            $endCycle = Application_Model_Entity_Settlement_Cycle::staticLoad($this->getData('ending_period'));
            $endCycle->changeDateFormat(['cycle_close_date'], true, true);
            $data = $startCycle->getCycleStartDate() . ' - ' . $endCycle->getCycleCloseDate();
        } else {
            $data = $this->getData('period_title');
        }

        return $data;
    }

    public function getExcelData($data, $iteratorName, $total = false)
    {
        $excelData = [
            [
                [
                    'value' => $this->getReportTitle(),
                    'style' => ['font' => ['bold' => true, 'name' => 'Arial', 'size' => 12, 'italic' => true]],
                ],
            ],
            [],
        ];
        if (isset($data['hideTitle'])) {
            $excelData = [];
        }

        if (!isset($data['hideCycleHeader']) || (isset($data['hideCycleHeader']) && $data['hideCycleHeader'] == false)) {
            $excelData = array_merge($excelData, [
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
                [],
            ]);
        }

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
        foreach ($data['contractors'] as $contractor) {
            foreach ($contractor[$iteratorName] as $entity) {
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
        }

        if ($total) {
            $excelData[] = $total;
        }

        return $excelData;
    }

    //-------------------------Helpers----------------------------------------------------------------------------------
    public function saveExcelReport($data, $iteratorName, $total = false)
    {
        $excelData = $this->getExcelData($data, $iteratorName, $total);

        $model = new Application_Model_File_Type_Xls($this->getFileName());
        if (isset($data['hideTitle'])) {
            $model->disableTitle = true;
        }
        $model->getFileFromArray($excelData);

        return $this;
    }

    public function getCycleIdForFilter()
    {
        if ($this->getDateFilterType() == Application_Model_Report_Reporting::DATE_RANGE) {
            if (in_array(
                $this->getType(),
                [
                    Application_Model_Report_Reporting::ACH_FILE,
                    Application_Model_Report_Reporting::CHECK_FILE,
                    Application_Model_Report_Reporting::RESERVE_ACCOUNT_BALANCES,
                    Application_Model_Report_Reporting::CONTRACTOR_1099,
                ]
            )) {
                $cycleType = [Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID];
            } else {
                $cycleType = [
                    Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID,
                    Application_Model_Entity_System_SettlementCycleStatus::VERIFIED_STATUS_ID,
                    Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID,
                ];
            }
            $ids = (new Application_Model_Entity_Settlement_Cycle())->getCollection()->addFilter(
                'cycle_start_date',
                $this->changeDateFormat($this->getRangeStartDate()),
                'GTE DATE'
            )->addFilter(
                'cycle_close_date',
                $this->changeDateFormat($this->getRangeEndDate()),
                'LTE DATE'
            )->addFilter('status_id', $cycleType, 'IN')->addFilterByUserRole()->getField('id');
            if (!(is_countable($ids) ? count($ids) : 0)) {
                $ids = [0];
            }

            return $ids;
        } elseif ($this->getDateFilterType() == Application_Model_Report_Reporting::SETTLEMENT_CYCLE) {
            $period = $this->getPeriod();
            if (!$period) {
                $period = 0;
            }

            return [$period];
        } elseif ($this->getDateFilterType() == Application_Model_Report_Reporting::SETTLEMENT_CYCLES) {
            if (in_array(
                $this->getType(),
                [
                    Application_Model_Report_Reporting::ACH_FILE,
                    Application_Model_Report_Reporting::CHECK_FILE,
                    Application_Model_Report_Reporting::RESERVE_ACCOUNT_BALANCES,
                    Application_Model_Report_Reporting::CONTRACTOR_1099,
                ]
            )) {
                $cycleType = [Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID];
            } else {
                $cycleType = [
                    Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID,
                    Application_Model_Entity_System_SettlementCycleStatus::VERIFIED_STATUS_ID,
                    Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID,
                ];
            }
            $ids = (new Application_Model_Entity_Settlement_Cycle())->getCollection()->addFilter(
                'cycle_start_date',
                Application_Model_Entity_Settlement_Cycle::staticLoad(
                    $this->getStartingPeriod()
                )->getCycleStartDate(),
                'GTE DATE'
            )->addFilter(
                'cycle_close_date',
                Application_Model_Entity_Settlement_Cycle::staticLoad($this->getEndingPeriod())->getCycleCloseDate(
                ),
                'LTE DATE'
            )->addFilter('status_id', $cycleType, 'IN')->addFilterByUserRole()->getField('id');
            if (!(is_countable($ids) ? count($ids) : 0)) {
                $ids = [0];
            }

            return $ids;
        } elseif ($this->getDateFilterType() == Application_Model_Report_Reporting::INVOICE_DATE) {
            $ids = (new Application_Model_Entity_Settlement_Cycle())->getCollection()->addFilterByUserRole()->getField(
                'id'
            );
            if (!(is_countable($ids) ? count($ids) : 0)) {
                $ids = [0];
            }

            return $ids;
        }
    }

    public function getDateRange()
    {
        $dataRange = [
            $this->changeDateFormat($this->getRangeStartDate()),
            $this->changeDateFormat($this->getRangeEndDate()),
        ];

        return $dataRange;
    }

    /**
     * @param $wrongDate
     * @param bool $fromDb
     * @return string
     */
    public function changeDateFormat($wrongDate, $fromDb = false, $short = false)
    {
        $fromFormat = 'MM-dd-yyyy';
        $toFormat = 'yyyy-MM-dd';
        if ($fromDb) {
            $fromFormat = 'yyyy-MM-dd';
            $toFormat = 'MM-dd-yyyy';
        }
        if ($wrongDate) {
            $wrongDate = new Zend_Date($wrongDate, $fromFormat);
            $rightDate = $wrongDate->toString($toFormat);
        } else {
            $rightDate = '';
        }

        return $rightDate;
    }

    public function getUser()
    {
        if (!$this->user) {
            $this->user = Application_Model_Entity_Accounts_User::getCurrentUser();
        }

        return $this->user;
    }

    public function getContractorId($configData = [])
    {
        if ($this->getUser()->isSpecialist()) {
            $contractorIds = [$this->getUser()->getRelatedEntity()->getId()];
        } else {
            if ($this->getSelectContractor() != self::SELECTED_CONTRACTORS) {
                $contractorCollection = (new Application_Model_Entity_Entity_Contractor())->getCollection(
                )->addFilterByCarrierContractor()->addFilterByVendorVisibility();
                if (isset($configData['order'])) {
                    foreach ($configData['order'] as $order => $direction) {
                        $contractorCollection->setOrder($order, $direction);
                    }
                }
                if ($this->getSelectContractor() != self::ALL_CONTRACTORS) {
                    if ($this->getSelectContractor() == self::MAIL_CONTRACTORS) {
                        $correspondenceMethod = Application_Model_Entity_Entity_Contact_Type::TYPE_ADDRESS;
                    } else {
                        $correspondenceMethod = Application_Model_Entity_Entity_Contact_Type::TYPE_CARRIER_DISTRIBUTES;
                    }

                    $contractorCollection->addFilterByCorrespondenceMethod($correspondenceMethod);
                }
                $contractorIds = $contractorCollection->getField('entity_id');
                if (!(is_countable($contractorIds) ? count($contractorIds) : 0)) {
                    $contractorIds = [0];
                }
            } else {
                if (!$contractorIds = json_decode((string) $this->getData('contractor_id'), true, 512, JSON_THROW_ON_ERROR)) {
                    $contractorIds = [0];
                }
            }
        }

        return $contractorIds;
    }

    public function getCarrierVendorId()
    {
        $carrierVendorIds = [];
        if ($this->getUser()->isOnboarding()) {
            $entity = $this->getUser()->getRelatedEntity();
            if ($entity->getEntityByType()->getStatus(
            ) == Application_Model_Entity_System_SystemValues::CONFIGURED_STATUS) {
                $carrierVendorIds = [$entity->getId()];
            }
        } else {
            if ($this->getSelectCarrierVendor() == self::ALL_CV) {
                $carrierVendorIds = (new Application_Model_Entity_Entity_Vendor())->getCollection(
                )->addConfiguredFilter()->addVisibilityFilterForUser()->getField('entity_id');
                if (($carrierId = $this->getUser()->getSelectedCarrier()->getEntityId()) || $this->getUser(
                )->getSelectedCarrier()->getStatus(
                ) == Application_Model_Entity_System_SystemValues::CONFIGURED_STATUS) {
                    $carrierVendorIds[] = $carrierId;
                }
            } else {
                if (!$carrierVendorIds = json_decode((string) $this->getData('carrier_vendor_id'), true, 512, JSON_THROW_ON_ERROR)) {
                    $carrierVendorIds = [0];
                }
            }
        }
        if (!(is_countable($carrierVendorIds) ? count($carrierVendorIds) : 0)) {
            $carrierVendorIds = [0];
        }

        return $carrierVendorIds;
    }

    public function getReserveAccountId()
    {
        if ($this->getSelectReserveAccount() == self::ALL_RA) {
            $raIds = (new Application_Model_Entity_Accounts_Reserve_Powerunit())->getCollection()->addNonDeletedFilter(
            )->addVisibilityFilterForUser()->getField('id');
            //            if (!$this->getUser()->isSpecialist()) {
            //                $raIds = array_merge($raIds, (new Application_Model_Entity_Accounts_Reserve_Vendor())->getCollection()->addNonDeletedFilter()->addVisibilityFilterForUser()->getField('reserve_account_id'));
            //            }
        } else {
            if (!$raIds = json_decode((string) $this->getData('reserve_account_id'), true, 512, JSON_THROW_ON_ERROR)) {
                $raIds = [0];
            }
        }

        return $raIds;
    }

    public function getReserveAccountContractorId()
    {
        if ($this->getSelectReserveAccountContractor() == self::ALL_RA) {
            $raIds = (new Application_Model_Entity_Accounts_Reserve_Powerunit())->getCollection()->addNonDeletedFilter(
            )->addVisibilityFilterForUser()->getField('id');
            //            if (!$this->getUser()->isSpecialist()) {
            //                $raIds = array_merge($raIds, (new Application_Model_Entity_Accounts_Reserve_Vendor())->getCollection()->addNonDeletedFilter()->addVisibilityFilterForUser()->getField('reserve_account_id'));
            //            }
        } else {
            if (!$raIds = json_decode((string) $this->getData('reserve_account_contractor_id'), true, 512, JSON_THROW_ON_ERROR)) {
                $raIds = [0];
            }
        }

        return $raIds;
    }

    public function getOrientation($type = null)
    {
        if ($type === null) {
            $type = $this->getType();
        }
        $orientation = match ($type) {
            self::PAYMENT_HISTORY, self::DEDUCTION_HISTORY => 'A4-L',
            self::CHECK_PRINTING_FILE => 'Letter',
            default => 'A4',
        };

        return $orientation;
    }

    public function getCss($type = null)
    {
        if ($type === null) {
            $type = $this->getType();
        }
        $cssPath = '/css/';
        $prefix = $this->getPrefix();
        $cssList = match ($type) {
            self::CHECK_PRINTING_FILE => [
                $cssPath . $prefix . 'pdf.css',
                $cssPath . $prefix . 'pdf-check.css',
            ],
            default => [$cssPath . $prefix . 'pdf.css'],
        };

        return $cssList;
    }

    public function getFontKey($type = null)
    {
        if ($type === null) {
            $type = $this->getType();
        }

        return match ($type) {
            self::CHECK_PRINTING_FILE => 's',
            default => 'c',
        };
    }

    public static function getFileTypeOptions()
    {
        return [
            Application_Model_File_Type_Pdf::TYPE => '*.pdf',
            Application_Model_File_Type_Xls::XLS_TYPE => '*.xls',
            Application_Model_File_Type_Xls::XLSX_TYPE => '*.xlsx',
            Application_Model_File_Type_Txt::TYPE => '*.txt',
            Application_Model_File_Type_Csv::TYPE => '*.csv',
        ];
    }

    public function getReportTitle()
    {
        return static::getReportTypeOptions()[$this->getType()];
    }

    public function getFileName($title = null)
    {
        if (!$this->getData('file_name')) {
            if (!$title) {
                $title = $this->getReportTitle();
            }
            $this->setData(
                'file_name',
                str_replace(' ', '_', (string) $title) . '_' . date('m-d-Y_H-i-s') . '.' . $this->getFileType()
            );
        }

        return $this->getData('file_name');
    }

    public function getView()
    {
        return $this->view;
    }
}
