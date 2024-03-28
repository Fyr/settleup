<?php

class Application_Model_Resource_Powerunit_Temp extends Application_Model_Base_Resource
{
    protected $_name = 'powerunit_temp';

    public function getInfoFields()
    {
        $infoFields = [
            'code' => 'ID',
            'carrier_id' => 'Division',
            'contractor_code' => 'Contractor Code',
            'start_date' => 'Start Date',
            'termination_date' => 'Termination Date',
            'status' => 'Status',
            'domicile' => 'Domicile',
            'plate_owner' => 'Plate Owner',
            'form2290' => '2290',
            'ifta_filing_owner' => 'IFTA Filing Owner',
            'status_id' => 'Uploaded File Status',
        ];

        return $infoFields;
    }

    public function getParentEntity(): Application_Model_Entity_Powerunit_Powerunit
    {
        return new Application_Model_Entity_Powerunit_Powerunit();
    }
}
