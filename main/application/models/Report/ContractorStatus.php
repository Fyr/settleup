<?php

class Application_Model_Report_ContractorStatus extends Application_Model_Report_Reporting
{
    protected $view = '/reporting/grid/contractor-vendor-status.phtml';
    public static $title = 'Contractor Status';

    public function getGridData()
    {
        $data = [
            'fields' => [
                'code' => 'ID',
                'company_name' => 'Company',
                'tax_id' => 'Fed Tax Id',
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
                'division' => 'Division',
                'department' => 'Dept',
                'route' => 'Route',
                'status_title' => 'Status',
            ],
            'callbacks' => [
                'tax_id' => Application_Model_Grid_Callback_Decrypt::class,
            ],
        ];

        $lastFieldsPart = [
            'start_date' => 'Start Date',
            'termination_date' => 'Termination Date',
            'rehire_date' => 'Restart Date',
        ];

        /** @var Application_Model_Entity_Collection_Entity_Contractor $contractorCollection */
        $contractorCollection = (new Application_Model_Entity_Entity_Contractor())->getCollection()->addFilter(
            'carrier_id',
            $this->getUser()->getSelectedCarrier()->getEntityId()
        )->addNonDeletedFilter()->addFilterByStatus($this->getSelectContractorStatus())->setOrder(
            'status_title',
            'ASC'
        );

        if (Application_Model_Entity_Accounts_User::getCurrentUser()->isVendor()) {
            $lastFieldsPart = ['vendor_status_title' => 'Vendor Status', ...$lastFieldsPart];
            $contractorCollection->vendorFilter();
            $contractorCollection->setOrder('vendor_status_title', 'ASC');
        }
        $contractorCollection->setOrder('company_name', 'ASC');

        $contractors = $contractorCollection->getItems();

        foreach ($contractors as $id => $contractor) {
            $contractors[$id]->changeDateFormat(['start_date', 'termination_date', 'rehire_date'], true, true);
        }
        $data['fields'] = [...$data['fields'], ...$lastFieldsPart];

        $data['contractors'] = $contractors;
        $data['key'] = 'contractors';
        $data['title'] = 'Contractor Status';

        if ($this->getAction() == Application_Model_Report_Reporting::DOWNLOAD_ACTION && in_array(
            $this->getFileType(),
            [Application_Model_File_Type_Xls::XLS_TYPE, Application_Model_File_Type_Xls::XLSX_TYPE]
        )) {
            $this->saveExcelContractorVendorStatus($data);
        }

        return $data;
    }

    public function saveExcelContractorVendorStatus($data)
    {
        $data['hideCycleHeader'] = true;
        $data['contractors'] = [['contractors' => $data['contractors']]];
        $this->saveExcelReport($data, 'contractors');
    }
}
