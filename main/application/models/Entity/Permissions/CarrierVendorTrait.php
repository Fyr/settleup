<?php

trait Application_Model_Entity_Permissions_CarrierVendorTrait
{
    /**
     * @return bool
     */
    public function checkPermissions()
    {
        if ($this->getDeleted()) {
            return false;
        }
        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        if ($entityId = $user->getCarrierEntityId()) {
            if ($this->getProviderId() == $entityId) {
                return true;
            }
            $vendorEntity = new Application_Model_Entity_Entity_Vendor();
            $vendorEntity->load($this->getProviderId(), 'entity_id');
            if ($vendorEntity->getCarrierId() == $entityId) {
                return true;
            }
        } elseif ($entityId = $user->getVendorEntityId()) {
            if ($this->getProviderId() == $entityId) {
                return true;
            }
        }

        return false;
    }
}
