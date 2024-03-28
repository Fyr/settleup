<?php

use Application_Model_Entity_Entity_Contractor as Contractor;

class Application_Model_Resource_Entity_ContractorTemp extends Application_Model_Base_Resource
{
    protected $_name = 'contractor_temp';

    public function getInfoFields(): array
    {
        return [
            'code' => 'Contractor ID',
            'company_name' => 'Company',
            'first_name' => 'First Name',
            'middle_initial' => 'Middle Initial',
            'last_name' => 'Last Name',
        ];
    }

    public function getParentEntity(): Contractor
    {
        return new Contractor();
    }
}
