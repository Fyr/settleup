<?php

trait Application_Model_Entity_Collection_ContractorFilterTrait
{
    /**
     * Filter collection by currently selected contractor
     *
     * @return Application_Model_Base_Collection
     * @var $contractorId int
     */
    public function addContractorFilter($contractorId = null)
    {
        if ($contractorId) {
            $this->addFilter('contractor_id', $contractorId);
        } else {
            $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();
            $contractorEntity = $userEntity->getEntity()->getCurrentContractor();
            if ($contractorEntity) {
                $this->addFilter('contractor_id', $contractorEntity->getEntityId());
            }
        }

        return $this;
    }
}
