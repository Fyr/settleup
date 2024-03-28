<?php

class Application_Model_Resource_Entity_Vendor extends Application_Model_Base_Resource
{
    protected $_name = 'vendor';
    protected $_pk = 'id';

    public function getInfoFields()
    {
        $infoFields = [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
        ];

        return $infoFields;
    }

    public function getInfoFieldsForPopup()
    {
        $infoFields = [
            'code' => 'ID',
            'name' => 'Vendor',
            'tax_id' => 'Federal Tax Id',
        ];

        return $infoFields;
    }
}
