<?php

class Application_Model_Resource_Entity_Contractor extends Application_Model_Base_Resource
{
    protected $_name = 'contractor';
    protected $_pk = 'id';
    protected $_titleColumn = 'company_name';

    public function getInfoFields()
    {
        $infoFields = [
            'code' => 'Code',
            //            'social_security_id' => 'Social Security ID',
            //            'tax_id'             => 'Federal Tax ID',
            'company_name' => 'Company Name',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
        ];

        return $infoFields;
    }

    public function getInfoFieldsForPopup()
    {
        $infoFields = [
            'code' => 'Code',
            'company_name' => 'Company',
            'tax_id' => 'Fed Tax ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'division' => 'Division',
            'department' => 'Department',
            'route' => 'Route',
        ];

        return $infoFields;
    }

    public function getInfoFieldsForReportPopup()
    {
        $infoFields = [
            'code' => 'Code',
            'company_name' => 'Company',
            'tax_id' => 'Fed Tax ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
        ];

        return $infoFields;
    }

    public function getInfoFieldsForListAction()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'company_name' => 'Contractor',
            'first_name' => 'Contact First Name',
            'last_name' => 'Contact Last Name',
            'start_date' => 'Start Date',
            'termination_date' => 'Termination Date',
            'rehire_date' => 'Restart Date',
            'status_title' => 'Status',
            'settlement_group' => 'Settlement Group',
        ];
    }

    public function getInfoFieldsForListActionVendor()
    {
        return [
            'code' => 'Code',
            'company_name' => 'Company',
            'tax_id' => 'Fed Tax ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'division' => 'Division',
            'department' => 'Dept',
            'route' => 'Route',
            'start_date' => 'Start Date',
            'termination_date' => 'Termination Date',
            'rehire_date' => 'Restart Date',
            'status_title' => 'Status',
            'vendor_status_title' => 'Vendor Status',
        ];
    }
}
