<?php

class Application_Model_Resource_Entity_Contact_Temp extends Application_Model_Base_Resource
{
    protected $_name = 'entity_contact_info_temp';

    public function getInfoFields()
    {
        return [
            'contact_type' => 'Type',
            'value' => 'Value',
        ];
    }

    /**
     * @return Application_Model_Entity_Deductions_Deduction
     */
    public function getParentEntity()
    {
        return new Application_Model_Entity_Entity_Contact_Info();
    }
}
