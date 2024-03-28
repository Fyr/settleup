<?php

class Application_Model_Resource_Entity_Permissions extends Application_Model_Base_Resource
{
    protected $_name = 'user_permissions';
    protected $_pk = 'id';
    protected $_titleColumn = 'id';

    public function getInfoFields()
    {
        $infoFields = [
            $this->getPrimaryKey() => '#',
        ];

        return $infoFields;
    }
}
