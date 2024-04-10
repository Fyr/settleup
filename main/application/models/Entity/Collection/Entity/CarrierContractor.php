<?php

class Application_Model_Entity_Collection_Entity_CarrierContractor extends Application_Model_Base_Collection
{
    //    public function _beforeLoad()
    //    {
    //        parent::_beforeLoad();
    //
    //        $this->addFieldsForSelect(
    //            new Application_Model_Entity_Entity_CarrierContractor(),
    //            'contractor_id',
    //            new Application_Model_Entity_Entity_Contractor(),
    //            'entity_id',
    //            array('tax_id', 'company_name', 'first_name', 'last_name', 'code',
    //                'division', 'department', 'route')
    //        );
    //
    //        $this->addFieldsForSelect(
    //            new Application_Model_Entity_Entity_CarrierContractor(),
    //            'status',
    //            new Application_Model_Entity_System_ContractorStatus(),
    //            'id',
    //            array('title')
    //        );
    //
    //        return $this;
    //
    //    }
    //
    //    /**
    //     * Filters payments collection by currently selected carrier
    //     * @return Application_Model_Entity_Collection_Payments_Payment
    //     */
    //    public function addCarrierFilter()
    //    {
    //        $carrierEntity = Application_Model_Entity_Accounts_User::
    //            getCurrentUser()->getEntity();
    //        $this->addFilter('carrier_id', $carrierEntity->getEntityId());
    //
    //        return $this;
    //
    //    }
    //
    //
    //    public function addFilterByVendorVisibility()
    //    {
    //        $entity = Application_Model_Entity_Accounts_User::getCurrentUser()->getEntity();
    //        if ($entity instanceof Application_Model_Entity_Entity_Vendor) {
    //            $this->addFieldsForSelect(
    //                (new Application_Model_Entity_Entity_CarrierContractor()),
    //                'contractor_id',
    //                (new Application_Model_Entity_Entity_ContractorVendor()),
    //                'contractor_id',
    //                array('contractor_vendor_vendor_id' => 'vendor_id', 'contractor_vendor_status' => 'status')
    //            );
    //            $this->addFilter('contractor_vendor_vendor_id', $entity->getEntityId());
    //            $this->addFilter('contractor_vendor_status', Application_Model_Entity_System_VendorStatus::STATUS_ACTIVE, '=', true, Application_Model_Base_Collection::WHERE_TYPE_AND, true);
    //            $this->addFilter('contractor_vendor_status', Application_Model_Entity_System_VendorStatus::STATUS_RESCINDED, '=', true, Application_Model_Base_Collection::WHERE_TYPE_OR, false);
    //        }
    //        return $this;
    //    }
}
