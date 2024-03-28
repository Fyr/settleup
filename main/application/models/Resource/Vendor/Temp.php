<?php


class Application_Model_Resource_Vendor_Temp extends Application_Model_Base_Resource
{
    protected $_name = 'vendor_temp';

    public function getInfoFields()
    {
        $infoFields = [
            'code' => 'Code',
            'name' => 'Name',
            'status_id' => 'Uploaded File Status',
        ];

        return $infoFields;
    }

    /**
     * @return Application_Model_Entity_Entity_Vendor
     */
    public function getParentEntity()
    {
        return new Application_Model_Entity_Entity_Vendor();
    }
}
