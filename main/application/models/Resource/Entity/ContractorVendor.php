<?php

class Application_Model_Resource_Entity_ContractorVendor extends Application_Model_Base_Resource
{
    protected $_name = 'contractor_vendor';
    protected $_pk = 'id';

    public function getInfoFields()
    {
        return [];
    }

    /**
     * get vendor by contractor id
     *
     * @param $contractor_id
     * @return array
     * @throws Zend_Exception
     * @throws Zend_Db_Select_Exception
     * @throws Zend_Db_Table_Exception
     */
    public function getVendorsByContractorId($contractor_id)
    {
        return $this->fetchAll(
            'contractor_id=' . $contractor_id
        )->toArray();
    }
}
