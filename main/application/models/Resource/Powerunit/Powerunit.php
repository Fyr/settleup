<?php

class Application_Model_Resource_Powerunit_Powerunit extends Application_Model_Base_Resource
{
    protected $_name = 'powerunit';
    protected $_pk = 'id';

    public function getInfoFields()
    {
        $infoFields = [
            'code' => 'ID',
            'carrier_id' => 'Division',
            'contractor_id' => 'Contractor ID',
            'contractor_code' => 'Contractor Code',
            'domicile' => 'Domicile',
        ];

        return $infoFields;
    }

    public function getInfoFieldsForDeductionPopup()
    {
        return [
            'code' => 'PU Code',
            'contractor_code' => 'Contractor Code',
            'company_name' => 'Company',
            'tax_id' => 'Fed Tax ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
        ];
    }

    public function getInfoFieldsShort()
    {
        return [
            'contractor_code' => 'Contractor',
            'code' => 'Code',
            'company_name' => 'Company',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
        ];
    }

    public function getInfoFieldsForPopup()
    {
        $infoFields = [
            'code' => 'ID',
            'carrier_id' => 'Division',
            'contractor_id' => 'Contractor ID',
            'contractor_code' => 'Contractor Code',
            'domicile' => 'Domicile',
        ];

        return $infoFields;
    }

    public function getInfoFieldsForReportPopup()
    {
        $infoFields = [
            'code' => 'ID',
        ];

        return $infoFields;
    }

    public function getInfoFieldsForListAction()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'division_id' => 'Division ID',
            'division_code' => 'Division Code',
            'contractor_id' => 'Contractor ID',
            'contractor_code' => 'Contractor Code',
            'domicile' => 'Domicile',
        ];
    }

    public function getInfoFieldsForListActionVendor()
    {
        return [
            'code' => 'ID',
        ];
    }
}
