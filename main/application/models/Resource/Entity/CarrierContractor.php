<?php

class Application_Model_Resource_Entity_CarrierContractor extends Application_Model_Base_Resource
{
    protected $_name = 'carrier_contractor';
    protected $_pk = 'id';

    public function getInfoFields()
    {
        return [
            'code' => 'ID',
            'company_name' => 'Company',
            'tax_id' => 'Fed Tax Id',
            'title' => 'Status',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'division' => 'Division',
            'department' => 'Dept',
            'route' => 'Route',
            'start_date' => 'Start Date',
            'termination_date' => 'Termination Date',
            'rehire_date' => 'Restart date',
        ];
    }
}
