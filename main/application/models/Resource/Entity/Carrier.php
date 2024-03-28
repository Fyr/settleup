<?php

class Application_Model_Resource_Entity_Carrier extends Application_Model_Base_Resource
{
    protected $_name = 'carrier';
    protected $_pk = 'id';
    protected $_titleColumn = 'name';

    public function getInfoFields()
    {
        $infoFields = [
            'id' => 'ID',
            'short_code' => 'Code',
            'name' => 'Name',
            'tax_id' => 'Federal Tax ID',
        ];

        return $infoFields;
    }

    public function getInfoFieldsForPopup()
    {
        $infoFields = [
            'id' => 'ID',
            'name' => 'Name',
            'tax_id' => 'Federal Tax Id',
        ];

        return $infoFields;
    }
}
