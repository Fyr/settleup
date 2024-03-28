<?php

trait Application_Model_Entity_Entity_ContractorVendorTrait
{
    public function getVendors()
    {
        $vendors = [];
        if ($this->getId()) {
            $vendors = (new Application_Model_Entity_Entity_ContractorVendor())->getCollection()->addFilter(
                'contractor_id',
                $this->getEntityId()
            )->getItems();
        }
        if (!count($vendors)) {
            $vendors = [
                (new Application_Model_Entity_Entity_ContractorVendor()),
            ];
        }

        return $vendors;
    }
}
