<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Accounts_UserEntity as UserEntity;

class Application_Model_Entity_Collection_Entity_Carrier extends Application_Model_Base_Collection
{
    use Application_Model_Entity_Collection_Entity_ConfiguredFilterTrait;

    public function addVisibilityFilterForUser($showAllForAdmin = false): self
    {
        $userEntity = User::getCurrentUser();
        if ($userEntity->isAdminOrSuperAdmin()) {
            if (!$showAllForAdmin) {
                $this->addFilter('entity_id', $userEntity->getSelectedCarrier()->getEntityId());
            }
        } else {
            $entityIds = (new UserEntity())
                ->getCollection()
                ->addFilterByUserId($userEntity->getId())
                ->getField('entity_id');
            $this->addFilter('entity_id', $entityIds ?: [0], 'IN');
        }

        return $this;
    }

    /**
     * Filters carriers collection in accordance with list of visibility
     *
     * @param int $userEntityId - Entity id of selected user
     * @return Application_Model_Entity_Collection_Entity_Carrier
     */
    public function addVisibilityFilter($userEntityId)
    {
        $userVisibilityEntity = new Application_Model_Entity_Accounts_UsersVisibility();
        $participantIds = $userVisibilityEntity->getCollection()->addFilter('entity_id', $userEntityId)->getField(
            'participant_id'
        );
        if ($participantIds == []) {
            array_push($participantIds, '0');
        }
        $participantIds[] = $userEntityId;
        $this->addFilter('entity_id', $participantIds, 'IN');

        return $this;
    }

    /**
     * Filters carriers collection in accordance with list of visibility except
     * currently added items
     *
     * @param int $userEntityId - Entity id of selected user
     * @return Application_Model_Entity_Collection_Entity_Carrier
     */
    public function addFilterByAddedEntities($userEntityId)
    {
        $carrierEntity = new Application_Model_Entity_Entity_Carrier();
        $entitiesId = $carrierEntity->getCollection()->addVisibilityFilter($userEntityId)->getField('entity_id');
        if ($entitiesId == []) {
            return $this;
        } else {
            $this->addFilter('entity_id', $entitiesId, 'NOT IN');
        }

        return $this;
    }

    public function addCarrierVendorFilter()
    {
        //        if ($contractorEntity = Application_Model_Entity_Accounts_User::getCurrentUser()->getSelectedContractor()) {
        //            $contractorsId[] = $contractorEntity->getEntityId();
        //        } else {
        //            $contractor = new Application_Model_Entity_Entity_Contractor();
        //            $contractorCollection = $contractor->getCollection()->addFilterByCarrierContractor();
        //            $contractorsId = $contractorCollection->getField('entity_id');
        //        }
        //        if (!count($contractorsId)) {
        //            $contractorsId[] = 0;
        //        }

        //        $contractorVendor = new Application_Model_Entity_Entity_ContractorVendor();
        //        $contractorCollection = $contractorVendor->getCollection()->addFilter('vendor_id', 1)
        //            ->addFilter('status', 0)->getField('vendor_id');
        //
        //        if (!count($vendorsId)) {
        //            $vendorsId[] = 0;
        //        }

        //        return $this->addFilter('entity_id', $vendorsId, 'IN');
        return $this;
    }

    public function getDeletedFieldName()
    {
        return 'entity.deleted';
    }

    public function addContractorStatusFilter()
    {
        if ($contractorId = User::getCurrentUser()->getData(
            'last_selected_contractor'
        )) {
            $contractor = new Application_Model_Entity_Entity_Contractor();
            $contractor->load($contractorId);
            if ($contractor->getCarrierStatusId() != Application_Model_Entity_System_VendorStatus::STATUS_ACTIVE) {
                return $this->getEmptyCollection();
            }
        }

        return $this;
    }
}
