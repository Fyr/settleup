<?php

class Application_Model_Entity_Collection_Entity_ContractorVendorTemp extends Application_Model_Base_Collection
{
    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_ContractorVendorTemp(),
            'status',
            new Application_Model_Entity_System_VendorStatus(),
            'id',
            ['status_title' => 'title']
        );

        return $this;
    }
}
