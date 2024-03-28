<?php

class Application_Model_Resource_Settlement_Group extends Application_Model_Base_Resource
{
    protected $_name = 'settlement_group';

    public function getInfoFields()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'division_name' => 'Division',
        ];
    }
}
