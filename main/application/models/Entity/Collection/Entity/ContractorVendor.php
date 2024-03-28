<?php

class Application_Model_Entity_Collection_Entity_ContractorVendor extends Application_Model_Base_Collection
{
    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_ContractorVendor(),
            'vendor_id',
            new Application_Model_Entity_Entity_Vendor(),
            'entity_id',
            ['name', 'vendor_code' => 'code']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_ContractorVendor(),
            'status',
            new Application_Model_Entity_System_VendorStatus(),
            'id',
            ['status_title' => 'title']
        );

        return $this;
    }

    /**
     * Filters payments collection by currently selected carrier
     *
     * @return Application_Model_Entity_Collection_Payments_Payment
     */
    public function addVendorFilter()
    {
        $contractorEntity = Application_Model_Entity_Accounts_User::getCurrentUser()->getEntity();
        $this->addFilter('contractor_id', $contractorEntity->getEntityId());

        return $this;
    }

    /**
     * filter by vendor
     *
     * @param $vendor_id
     * @return $this
     */
    public function filterByVendor($vendor_id)
    {
        $this->addFilter('vendor_id', $vendor_id);

        return $this;
    }

    /**
     * filter by contractor
     *
     * @param $contractor_id
     * @return $this
     */
    public function filterByContractor($contractor_id)
    {
        $this->addFilter('contractor_id', $contractor_id);

        return $this;
    }

    public function statusFilter($statusId)
    {
        $this->addFilter('status', $statusId);

        return $this;
    }
}
