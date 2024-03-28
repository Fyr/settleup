<?php

trait Application_Model_Entity_Permissions_CarrierTrait
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
        $carrierEntityId = $user->getCarrierEntityId();
        if ($this->getCarrierId() == $carrierEntityId) {
            return true;
        }
        if ($user->isContractor()) {
            if ($this->getEntityId() == $user->getEntityId()) {
                return true;
            }
        }
        if ($user->isVendor()) {
            $contractorVendor = new Application_Model_Entity_Entity_ContractorVendor();
            $contractorVendor->load([
                'vendor_id' => $user->getEntityId(),
                'contractor_id' => $this->getEntityId(),
            ]);
            if ($contractorVendor->getId()) {
                if ($contractorVendor->getStatus(
                ) == Application_Model_Entity_System_VendorStatus::STATUS_ACTIVE || $contractorVendor->getStatus(
                ) == Application_Model_Entity_System_VendorStatus::STATUS_RESCINDED) {
                    return true;
                }
            }
        }

        return false;
    }
}
