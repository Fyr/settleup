<?php

use Application_Model_Entity_Entity_ContractorVendor as ContractorVendor;

class Application_Model_Resource_Entity_ContractorVendorTemp extends Application_Model_Base_Resource
{
    protected $_name = 'contractor_vendor_temp';

    public function getInfoFields(): array
    {
        return [
            'vendor_code' => 'Vendor ID',
            'status' => 'Vendor Status',
        ];
    }

    public function getParentEntity(): ContractorVendor
    {
        return new ContractorVendor();
    }
}
