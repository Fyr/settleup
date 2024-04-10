<?php

class Application_Form_Reporting extends Application_Form_Base
{
    protected $user;

    public function init()
    {
        $this->setName('reporting');
        parent::init();

        $action = new Application_Form_Element_Hidden('action');
        $action->setValue(Application_Model_Report_Reporting::VIEW_ACTION);

        $type = new Zend_Form_Element_Select('type');
        $type->setMultiOptions($this->getUserReportTypeOptions())->setLabel('Report')->setValue(
            array_keys($this->getUserReportTypeOptions())[0]
        );

        $dateFilterType = new Zend_Form_Element_Select('date_filter_type');
        $dateFilterType->setLabel('View By')->setMultiOptions(
            Application_Model_Report_Reporting::getDateFilterTypeOptions()
        );

        $year = new Zend_Form_Element_Select('year');
        $year->setLabel('Year');

        $startingYear = new Zend_Form_Element_Select('starting_year');
        $startingYear->setLabel('Starting Period');

        $endingYear = new Zend_Form_Element_Select('ending_year');
        $endingYear->setLabel('Ending Period');

        $startingPeriod = new Zend_Form_Element_Select('starting_period');
        $endingPeriod = new Zend_Form_Element_Select('ending_period');

        $invoiceStartDate = new Zend_Form_Element_Text('invoice_start_date');
        $invoiceStartDate->setLabel('Date Range');
        $invoiceStartDate->addValidator('DateDatetime');

        $invoiceEndDate = new Zend_Form_Element_Text('invoice_end_date');
        $invoiceEndDate->setLabel('-');
        $invoiceEndDate->addValidator('DateDatetime');

        $period = new Zend_Form_Element_Select('period');
        $period->setLabel('Period');

        $rangeStartDate = new Zend_Form_Element_Text('range_start_date');
        $rangeStartDate->setLabel('Range');
        $rangeStartDate->addValidator('DateDatetime');

        $rangeEndDate = new Zend_Form_Element_Text('range_end_date');
        $rangeEndDate->setLabel('-');
        $rangeEndDate->addValidator('DateDatetime');

        $selectCheckEntityType = new Zend_Form_Element_Select('select_check_entity_type');
        $selectCheckEntityType->setMultiOptions(
            Application_Model_Report_Reporting::getCheckEntityTypeOptions()
        )->setLabel('Selection');

        $selectEntityType = new Zend_Form_Element_Select('select_entity_type');
        $selectEntityType->setMultiOptions(Application_Model_Report_Reporting::getEntityTypeOptions())->setLabel(
            'Selection'
        );

        $selectContractorStatus = new Zend_Form_Element_Select('select_contractor_status');
        $selectContractorStatus->setMultiOptions(
            Application_Model_Report_Reporting::getSelectContractorStatusOptions()
        )->setLabel('Select Contractor Status');

        $selectVendorStatus = new Zend_Form_Element_Select('select_vendor_status');
        $selectVendorStatus->setMultiOptions(
            Application_Model_Report_Reporting::getSelectVendorStatusOptions()
        )->setLabel('Select Approval Status');

        $selectContractor = new Zend_Form_Element_Select('select_contractor');
        $selectContractor->setMultiOptions(Application_Model_Report_Reporting::getSelectContractorOptions())->setLabel(
            'Select Contractor'
        );

        $contractorId = new Application_Form_Element_Hidden('contractor_id');
        $contractorId->setValue("[]");
        $contractorIdTitle = new Zend_Form_Element_Text('contractor_id_title');
        $contractorIdTitle->setLabel('Contractor')->setAttrib('href', '#contractor_id_modal')->setAttrib(
            'data-toggle',
            'modal'
        );

        $selectRA = new Zend_Form_Element_Select('select_reserve_account');
        $selectRA->setMultiOptions(Application_Model_Report_Reporting::getSelectRAOptions())->setLabel(
            'Select Reserve Account'
        );

        $raId = new Application_Form_Element_Hidden('reserve_account_id');
        $raId->setValue("[]");
        $raIdTitle = new Zend_Form_Element_Text('reserve_account_id_title');
        $raIdTitle->setLabel('Reserve Account')->setAttrib('href', '#reserve_account_id_modal')->setAttrib(
            'data-toggle',
            'modal'
        );

        $selectRAC = new Zend_Form_Element_Select('select_reserve_account_contractor');
        $selectRAC->setMultiOptions(Application_Model_Report_Reporting::getSelectRAOptions())->setLabel(
            'Select Reserve Account Contractor'
        );

        $racId = new Application_Form_Element_Hidden('reserve_account_contractor_id');
        $racId->setValue("[]");
        $racIdTitle = new Zend_Form_Element_Text('reserve_account_contractor_id_title');
        $racIdTitle->setLabel('Reserve Account')->setAttrib('href', '#reserve_account_contractor_id_modal')->setAttrib(
            'data-toggle',
            'modal'
        );

        $selectCV = new Zend_Form_Element_Select('select_carrier_vendor');
        $selectCV->setMultiOptions(Application_Model_Report_Reporting::getSelectCvOptions())->setLabel('Select Vendor');

        $cvId = new Application_Form_Element_Hidden('carrier_vendor_id');
        $cvId->setValue("[]");
        $cvIdTitle = new Zend_Form_Element_Text('carrier_vendor_id_title');
        $cvIdTitle->setLabel('Vendor')->setAttrib('href', '#carrier_vendor_id_modal')->setAttrib(
            'data-toggle',
            'modal'
        );

        $fileType = new Zend_Form_Element_Select('file_type');
        $fileType->setLabel('Download File Type')->setMultiOptions(
            Application_Model_Report_Reporting::getFileTypeOptions()
        );

        $this->addElements(
            [
                $action,
                $type,
                $dateFilterType,
                $year,
                $period,
                $rangeStartDate,
                $rangeEndDate,
                $selectContractorStatus,
                $selectVendorStatus,
                $selectCheckEntityType,
                $selectEntityType,
                $selectContractor,
                $contractorId,
                $contractorIdTitle,
                $selectRA,
                $raId,
                $raIdTitle,
                $selectRAC,
                $racId,
                $racIdTitle,
                $selectCV,
                $cvId,
                $cvIdTitle,
                $fileType,
                $invoiceStartDate,
                $invoiceEndDate,
                $startingYear,
                $startingPeriod,
                $endingYear,
                $endingPeriod,
            ]
        );

        $this->setDefaultDecorators(
            [
                'type',
                'date_filter_type',
                'year',
                'period',
                'range_start_date',
                'range_end_date',
                'select_contractor_status',
                'select_vendor_status',
                'select_check_entity_type',
                'select_entity_type',
                'select_contractor',
                'contractor_id_title',
                'select_reserve_account',
                'reserve_account_id_title',
                'select_reserve_account_contractor',
                'reserve_account_contractor_id_title',
                'select_carrier_vendor',
                'carrier_vendor_id_title',
                'file_type',
                'invoice_start_date',
                'invoice_end_date',
                'starting_period',
                'ending_period',
                'starting_year',
                'ending_year',
            ]
        );
    }

    public function configure()
    {
        if (!$this->type->getValue()) {
            $this->type->setValue(array_keys($this->type->getMultiOptions())[0]);
        }
        //        $this->date_filter_type->setMultiOptions($this->getDateFilterTypeOptions($this->type->getValue()));
        if (!$this->date_filter_type->getValue()) {
            $this->date_filter_type->setValue(array_keys($this->getDateFilterTypeOptions($this->type->getValue()))[0]);
        }
        if (!$this->select_contractor->getValue()) {
            $this->select_contractor->setValue(Application_Model_Report_Reporting::ALL_CONTRACTORS);
        }
        if (!$this->select_reserve_account->getValue()) {
            $this->select_reserve_account->setValue(Application_Model_Report_Reporting::ALL_RA);
        }
        if (!$this->select_reserve_account_contractor->getValue()) {
            $this->select_reserve_account_contractor->setValue(Application_Model_Report_Reporting::ALL_RA);
        }
        if (!$this->select_carrier_vendor->getValue()) {
            $this->select_carrier_vendor->setValue(Application_Model_Report_Reporting::ALL_CV);
        }
        if (!$this->select_contractor_status->getValue()) {
            $this->select_contractor_status->setValue(0);
        }
        if (!$this->select_vendor_status->getValue()) {
            $this->select_vendor_status->setValue(0);
        }
        if (!$this->select_check_entity_type->getValue()) {
            $this->select_check_entity_type->setValue(0);
        }
        if (!$this->select_entity_type->getValue()) {
            $this->select_entity_type->setValue(0);
        }

        return $this;
    }

    public function showField($type, $field)
    {
        return in_array($field, $this->getUserAdditionalFilterByType($type));
    }

    public function getDateFilterTypeOptions($type)
    {
        $model = new Application_Model_Report_Reporting();
        $model->setType($type);
        foreach ($this->getElements() as $elementName => $element) {
            $model->setData(
                $elementName,
                $element->getValue()
            );
        }
        $model = $model->getModel();
        if ($model->dateFilterOptions) {
            return $model->dateFilterOptions;
        }

        $options = Application_Model_Report_Reporting::getDateFilterTypeOptions();
        switch ($type) {
            case (Application_Model_Report_Reporting::ACH_FILE):
            case (Application_Model_Report_Reporting::CHECK_FILE):
            case (Application_Model_Report_Reporting::CHECK_PRINTING_FILE):
            case (Application_Model_Report_Reporting::CONTRACTOR_SETTLEMENT_STATEMENT):
            case (Application_Model_Report_Reporting::SETTLEMENT_RECONCILIATION):
            case (Application_Model_Report_Reporting::RESERVE_ACCOUNT_BALANCES):
            case (Application_Model_Report_Reporting::DEDUCTION_REMITTANCE_FILE):
            case (Application_Model_Report_Reporting::UNFUNDED_DEDUCTIONS):
                unset($options[Application_Model_Report_Reporting::DATE_RANGE]);
                break;
            case (Application_Model_Report_Reporting::CONTRACTOR_1099):
                unset($options[Application_Model_Report_Reporting::SETTLEMENT_CYCLE]);
                break;
            case (Application_Model_Report_Reporting::CONTRACTOR_STATUS):
            case (Application_Model_Report_Reporting::VENDOR_STATUS):
            case (Application_Model_Report_Reporting::CONTRACTOR_EXPORT_FILE):
                $options = [];
                break;
            case (Application_Model_Report_Reporting::RESERVE_ACCOUNT_HISTORY):
            case (Application_Model_Report_Reporting::RESERVE_ACCOUNT_CONTRACTOR_HISTORY):
            case (Application_Model_Report_Reporting::PAYMENT_HISTORY):
            case (Application_Model_Report_Reporting::DEDUCTION_HISTORY):
            case (Application_Model_Report_Reporting::DISBURSEMENTS):
                break;
            default:
                break;
        }
        unset($options[Application_Model_Report_Reporting::SETTLEMENT_CYCLES]);
        unset($options[Application_Model_Report_Reporting::INVOICE_DATE]);

        return $options;
    }

    public function getAllDateFilterTypeOptions()
    {
        $options = [];
        foreach ($this->getUserReportTypeOptions() as $type => $title) {
            $options[$type] = array_keys($this->getDateFilterTypeOptions($type));
        }

        return $options;
    }

    public function getAllPeriodOptions()
    {
        return [
            Application_Model_Report_Reporting::ACH_FILE => false,
            Application_Model_Report_Reporting::CHECK_FILE => false,
            Application_Model_Report_Reporting::CHECK_PRINTING_FILE => false,
            Application_Model_Report_Reporting::RESERVE_ACCOUNT_BALANCES => false,
            Application_Model_Report_Reporting::CONTRACTOR_1099 => false,
            Application_Model_Report_Reporting::DEDUCTION_REMITTANCE_FILE => true,
            Application_Model_Report_Reporting::UNFUNDED_DEDUCTIONS => true,
            Application_Model_Report_Reporting::CONTRACTOR_SETTLEMENT_STATEMENT => true,
            Application_Model_Report_Reporting::SETTLEMENT_RECONCILIATION => true,
            Application_Model_Report_Reporting::PAYMENT_HISTORY => true,
            Application_Model_Report_Reporting::DEDUCTION_HISTORY => true,
            Application_Model_Report_Reporting::DISBURSEMENTS => false,
            Application_Model_Report_Reporting::RESERVE_ACCOUNT_HISTORY => true,
            Application_Model_Report_Reporting::RESERVE_ACCOUNT_CONTRACTOR_HISTORY => true,
            Application_Model_Report_Reporting::CONTRACTOR_STATUS => true,
            Application_Model_Report_Reporting::VENDOR_STATUS => false,
            Application_Model_Report_Reporting::CONTRACTOR_EXPORT_FILE => false,
        ];
    }

    public function getFileTypeOptions($type)
    {
        $options = match ($type) {
            Application_Model_Report_Reporting::ACH_FILE => [Application_Model_File_Type_Txt::TYPE => '*.txt'],
            Application_Model_Report_Reporting::CHECK_FILE, Application_Model_Report_Reporting::DEDUCTION_REMITTANCE_FILE => [Application_Model_File_Type_Csv::TYPE => '*.csv'],
            Application_Model_Report_Reporting::CONTRACTOR_EXPORT_FILE, Application_Model_Report_Reporting::UNFUNDED_DEDUCTIONS => [
                Application_Model_File_Type_Xls::XLS_TYPE => '*.xls',
                Application_Model_File_Type_Xls::XLSX_TYPE => '*.xlsx',
            ],
            Application_Model_Report_Reporting::CONTRACTOR_SETTLEMENT_STATEMENT, Application_Model_Report_Reporting::CHECK_PRINTING_FILE => [
                Application_Model_File_Type_Pdf::TYPE => '*.pdf',
            ],
            Application_Model_Report_Reporting::RESERVE_ACCOUNT_BALANCES, Application_Model_Report_Reporting::RESERVE_ACCOUNT_HISTORY, Application_Model_Report_Reporting::RESERVE_ACCOUNT_CONTRACTOR_HISTORY, Application_Model_Report_Reporting::SETTLEMENT_RECONCILIATION, Application_Model_Report_Reporting::CONTRACTOR_1099, Application_Model_Report_Reporting::CONTRACTOR_STATUS, Application_Model_Report_Reporting::VENDOR_STATUS, Application_Model_Report_Reporting::PAYMENT_HISTORY, Application_Model_Report_Reporting::DEDUCTION_HISTORY, Application_Model_Report_Reporting::DISBURSEMENTS => [
                Application_Model_File_Type_Pdf::TYPE => '*.pdf',
                Application_Model_File_Type_Xls::XLS_TYPE => '*.xls',
                Application_Model_File_Type_Xls::XLSX_TYPE => '*.xlsx',
            ],
            default => [],
        };

        return $options;
    }

    public function getAllFileTypeOptions()
    {
        $fileTypeOptions = [];
        foreach ($this->getUserReportTypeOptions() as $type => $title) {
            $fileTypeOptions[$type] = $this->getFileTypeOptions($type);
        }

        return $fileTypeOptions;
    }

    public function getAdditionalFilterByType($type)
    {
        switch ($type) {
            case (Application_Model_Report_Reporting::DEDUCTION_REMITTANCE_FILE):
            case (Application_Model_Report_Reporting::UNFUNDED_DEDUCTIONS):
                if ($this->getUser()->isManager() || $this->getUser()->isAdminOrSuperAdmin()) {
                    $fields = [
                        'carrier_vendor',
                    ];
                } else {
                    $fields = [];
                }
                break;
            case (Application_Model_Report_Reporting::CONTRACTOR_EXPORT_FILE):
            case (Application_Model_Report_Reporting::CONTRACTOR_STATUS):
                $fields = [
                    'contractor_status',
                ];
                break;
            case (Application_Model_Report_Reporting::VENDOR_STATUS):
                if ($this->getUser()->isOnboarding()) {
                    $fields = [
                        'contractor_status',
                        'vendor_status',
                    ];
                } else {
                    $fields = [
                        'carrier_vendor',
                        'contractor_status',
                        'vendor_status',
                    ];
                }
                break;
            case (Application_Model_Report_Reporting::CHECK_FILE):
            case (Application_Model_Report_Reporting::CHECK_PRINTING_FILE):
                $fields = [
                    'check_entity_type',
                ];
                break;
            case (Application_Model_Report_Reporting::ACH_FILE):
                $fields = [
                    'entity_type',
                ];
                break;
            case (Application_Model_Report_Reporting::CONTRACTOR_1099):
            case (Application_Model_Report_Reporting::PAYMENT_HISTORY):
            case (Application_Model_Report_Reporting::CONTRACTOR_SETTLEMENT_STATEMENT):
                if ($this->getUser()->isSpecialist()) {
                    $fields = [];
                } else {
                    $fields = [
                        'contractor',
                    ];
                }
                break;
            case (Application_Model_Report_Reporting::SETTLEMENT_RECONCILIATION):
                $fields = [];
                break;
            case (Application_Model_Report_Reporting::DEDUCTION_HISTORY):
                if ($this->getUser()->isSpecialist()) {
                    $fields = [
                        'carrier_vendor',
                    ];
                } elseif ($this->getUser()->isOnboarding()) {
                    $fields = [
                        'contractor',
                    ];
                } else {
                    $fields = [
                        'contractor',
                        'carrier_vendor',
                    ];
                }
                break;
            case (Application_Model_Report_Reporting::RESERVE_ACCOUNT_HISTORY):
            case (Application_Model_Report_Reporting::RESERVE_ACCOUNT_BALANCES):
                $fields = [
                    'contractor',
                    'reserve_account',
                ];
                break;
            case (Application_Model_Report_Reporting::RESERVE_ACCOUNT_CONTRACTOR_HISTORY):
                $fields = [
                    'reserve_account_contractor',
                ];
                break;
            default:
                $fields = [];
        }

        return $fields;
    }

    public function getUserAdditionalFilterByType($type)
    {
        return $this->getAdditionalFilterByType($type);
    }

    public function getAllUserAdditionalFilterByType()
    {
        $additionalFilters = [];
        foreach ($this->getUserReportTypeOptions() as $type => $title) {
            $additionalFilters[$type] = $this->getUserAdditionalFilterByType($type);
        }

        return $additionalFilters;
    }

    public function getUser()
    {
        if (!$this->user) {
            $this->user = Application_Model_Entity_Accounts_User::getCurrentUser();
        }

        return $this->user;
    }

    public function getUserReportTypeOptions()
    {
        $types = Application_Model_Report_Reporting::getReportTypeOptions();
        switch (true) {
            case $this->getUser()->isManager():
                if (!$this->getUser()->hasPermission(Application_Model_Entity_Entity_Permissions::REPORTING_GENERAL)) {
                    unset($types[Application_Model_Report_Reporting::CONTRACTOR_SETTLEMENT_STATEMENT]);
                    unset($types[Application_Model_Report_Reporting::PAYMENT_HISTORY]);
                    unset($types[Application_Model_Report_Reporting::DEDUCTION_HISTORY]);
                    unset($types[Application_Model_Report_Reporting::CONTRACTOR_STATUS]);
                    unset($types[Application_Model_Report_Reporting::VENDOR_STATUS]);
                    unset($types[Application_Model_Report_Reporting::CONTRACTOR_EXPORT_FILE]);
                    unset($types[Application_Model_Report_Reporting::UNFUNDED_DEDUCTIONS]);
                }
                if (!$this->getUser()->hasPermission(
                    Application_Model_Entity_Entity_Permissions::REPORTING_ACH_CHECK
                )) {
                    unset($types[Application_Model_Report_Reporting::ACH_FILE]);
                    unset($types[Application_Model_Report_Reporting::CHECK_FILE]);
                    unset($types[Application_Model_Report_Reporting::CHECK_PRINTING_FILE]);
                }
                if (!$this->getUser()->hasPermission(
                    Application_Model_Entity_Entity_Permissions::REPORTING_DEDUCTION_REMITTANCE_FILE
                )) {
                    unset($types[Application_Model_Report_Reporting::DEDUCTION_REMITTANCE_FILE]);
                }
                if (!$this->getUser()->hasPermission(
                    Application_Model_Entity_Entity_Permissions::REPORTING_SETTLEMENT_RECONCILIATION
                )) {
                    unset($types[Application_Model_Report_Reporting::SETTLEMENT_RECONCILIATION]);
                }
                unset($types[Application_Model_Report_Reporting::RESERVE_ACCOUNT_CONTRACTOR_HISTORY]);
                break;
            case $this->getUser()->isOnboarding():
                if (!$this->getUser()->hasPermission(
                    Application_Model_Entity_Entity_Permissions::REPORTING_DEDUCTION_REMITTANCE_FILE
                )) {
                    unset($types[Application_Model_Report_Reporting::DEDUCTION_REMITTANCE_FILE]);
                }
                if (!$this->getUser()->hasPermission(Application_Model_Entity_Entity_Permissions::REPORTING_GENERAL)) {
                    unset($types[Application_Model_Report_Reporting::DEDUCTION_HISTORY]);
                    unset($types[Application_Model_Report_Reporting::CONTRACTOR_STATUS]);
                    unset($types[Application_Model_Report_Reporting::UNFUNDED_DEDUCTIONS]);
                }
                unset($types[Application_Model_Report_Reporting::CONTRACTOR_EXPORT_FILE]);
                unset($types[Application_Model_Report_Reporting::DISBURSEMENTS]);
                unset($types[Application_Model_Report_Reporting::ACH_FILE]);
                unset($types[Application_Model_Report_Reporting::CHECK_FILE]);
                unset($types[Application_Model_Report_Reporting::CHECK_PRINTING_FILE]);
                unset($types[Application_Model_Report_Reporting::CONTRACTOR_SETTLEMENT_STATEMENT]);
                unset($types[Application_Model_Report_Reporting::SETTLEMENT_RECONCILIATION]);
                unset($types[Application_Model_Report_Reporting::PAYMENT_HISTORY]);
                unset($types[Application_Model_Report_Reporting::CONTRACTOR_1099]);
                unset($types[Application_Model_Report_Reporting::RESERVE_ACCOUNT_CONTRACTOR_HISTORY]);
                break;
            case $this->getUser()->isSpecialist():
                unset($types[Application_Model_Report_Reporting::DISBURSEMENTS]);
                unset($types[Application_Model_Report_Reporting::ACH_FILE]);
                unset($types[Application_Model_Report_Reporting::CHECK_FILE]);
                unset($types[Application_Model_Report_Reporting::CHECK_PRINTING_FILE]);
                unset($types[Application_Model_Report_Reporting::SETTLEMENT_RECONCILIATION]);
                unset($types[Application_Model_Report_Reporting::RESERVE_ACCOUNT_BALANCES]);
                unset($types[Application_Model_Report_Reporting::CONTRACTOR_STATUS]);
                unset($types[Application_Model_Report_Reporting::DEDUCTION_REMITTANCE_FILE]);
                unset($types[Application_Model_Report_Reporting::VENDOR_STATUS]);
                unset($types[Application_Model_Report_Reporting::RESERVE_ACCOUNT_HISTORY]);
                unset($types[Application_Model_Report_Reporting::CONTRACTOR_EXPORT_FILE]);
                unset($types[Application_Model_Report_Reporting::UNFUNDED_DEDUCTIONS]);
                break;
            default:
                unset($types[Application_Model_Report_Reporting::RESERVE_ACCOUNT_CONTRACTOR_HISTORY]);
                break;
        }

        return $types;
    }

    public function getAllNoCycleOptions()
    {
        $noCycleOptions = [];
        foreach ($this->getAllDateFilterTypeOptions() as $type => $options) {
            if (!$options) {
                $noCycleOptions[$type] = true;
            } else {
                $noCycleOptions[$type] = false;
            }
        }

        return $noCycleOptions;
    }
}
