<?php

class Application_Model_Report_VendorStatus extends Application_Model_Report_Reporting
{
    protected $view = '/reporting/grid/contractor-vendor-status.phtml';
    public static $title = 'Vendor Approval Status';

    public function getGridData()
    {
        $data = [
            'fields' => [
                'code' => 'ID',
                'company_name' => 'Company',
                'tax_id' => 'Fed Tax ID',
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
                'division' => 'Division',
                'department' => 'Dept',
                'route' => 'Route',
                'status_title' => 'Contractor Status',
                'vendor_name' => 'Vendor',
                'vendor_status_title' => 'Approval Status',
            ],
            'callbacks' => [
                'tax_id' => 'Application_Model_Grid_Callback_Decrypt',
            ],
        ];

        if (($vendorStatus = $this->getSelectVendorStatus()) == 1) {
            $vendorStatus = null;
        }
        $contractorCollection = (new Application_Model_Entity_Entity_Contractor())->getCollection()->addFilterByStatus(
            $this->getSelectContractorStatus()
        )->addVendorStatusFilter($this->getCarrierVendorId()[0], $vendorStatus);

        $data['contractors'] = $contractorCollection->getItems();

        $data['key'] = 'contractors';
        $data['title'] = 'Vendor Approval Status';
        $data['hideCycleHeader'] = true;

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
