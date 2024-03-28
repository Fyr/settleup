<?php

class Application_Model_Entity_Collection_Entity_Vendor extends Application_Model_Base_Collection
{
    use Application_Model_Entity_Collection_Entity_ConfiguredFilterTrait;

    public function addCarrierName()
    {
        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_Vendor(),
            'carrier_id',
            new Application_Model_Entity_Entity_Carrier(),
            'entity_id',
            ['carrier_name' => 'name']
        );

        return $this;
    }

    /**
     * Adds visibility filter for current user
     *
     * @return Application_Model_Entity_Collection_Entity_Vendor
     * @var bool $showAllForAdmin
     */
    public function addVisibilityFilterForUser($params = [true, false])
    {
        if (!is_array($params)) {
            $params = [$params, false];
        }
        if (Application_Model_Entity_Accounts_User::getCurrentUser()->getUserRoleID(
        ) != Application_Model_Entity_System_UserRoles::VENDOR_ROLE_ID) {
            $this->addCarrierVendorFilter($params[0], $params[1]);
        } else {
            $this->addFilter(
                'entity_id',
                Application_Model_Entity_Accounts_User::getCurrentUser()->getEntity()->getEntityId()
            );
        }

        return $this;
    }

    /**
     * Filters vendors collection in accordance with list of visibility
     *
     * @param int $userEntityId - Entity id of selected user
     * @return Application_Model_Entity_Collection_Entity_Vendor
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
     * Filters vendors collection in accordance with list of visibility except
     * currently added items
     *
     * @param int $userEntityId - Entity id of selected user
     * @return Application_Model_Entity_Collection_Entity_Vendor
     */
    public function addFilterByAddedEntities($userEntityId)
    {
        $vendorEntity = new Application_Model_Entity_Entity_Vendor();
        $entitiesId = $vendorEntity->getCollection()->addVisibilityFilter($userEntityId)->getField('entity_id');
        if ($entitiesId == []) {
            return $this;
        } else {
            $this->addFilter('entity_id', $entitiesId, 'NOT IN');
        }

        return $this;
    }

    public function addCarrierVendorFilter($onlyActive = true, $ignoreCurrentSelectedContractor = false)
    {
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();
        if (!$ignoreCurrentSelectedContractor && $contractorEntity = Application_Model_Entity_Accounts_User::getCurrentUser(
        )->getSelectedContractor()) {
            $contractorsId = [$contractorEntity->getEntityId()];
        } else {
            if (!$onlyActive) {
                $contractorsId = (new Application_Model_Entity_Entity_Contractor())->getCollection()->addFilter(
                    'carrier_id',
                    $userEntity->getSelectedCarrier()->getEntityId()
                )->addFilter('status', Application_Model_Entity_System_ContractorStatus::STATUS_ACTIVE)->getField(
                    'entity_id'
                );
            } else {
                $this->addFilter('carrier_id', $userEntity->getSelectedCarrier()->getEntityId(), '=');

                return $this;
            }
        }
        if (!(is_countable($contractorsId) ? count($contractorsId) : 0)) {
            $contractorsId[] = 0;
        }
        $contractorVendor = new Application_Model_Entity_Entity_ContractorVendor();
        $vendorsId = $contractorVendor->getCollection()->addFilter('contractor_id', $contractorsId, 'IN')->addFilter(
            'status',
            0
        )->getField('vendor_id');
        if (!(is_countable($vendorsId) ? count($vendorsId) : 0)) {
            $vendorsId[] = 0;
        }
        $this->addFilter('entity_id', $vendorsId, 'IN');

        return $this;
    }

    public function getDeletedFieldName()
    {
        return 'entity.deleted';
    }

    /**
     * @return $this
     */
    public function addContractorStatusFilter()
    {
        if ($contractorId = Application_Model_Entity_Accounts_User::getCurrentUser()->getData(
            'last_selected_contractor'
        )) {
            $contractor = new Application_Model_Entity_Entity_Contractor();
            $contractor->load($contractorId);
            $contractorVendorEntity = new Application_Model_Entity_Entity_ContractorVendor();
            /** @var $contractorVendorCollection Application_Model_Entity_Collection_Entity_ContractorVendor */
            $contractorVendorCollection = $contractorVendorEntity->getCollection();
            $contractorVendorCollection->filterByContractor($contractor->getEntityId())->addFilter(
                'status',
                Application_Model_Entity_System_VendorStatus::STATUS_ACTIVE
            );
            $items = $contractorVendorCollection->getItems('vendor_id');
            if (count($items)) {
                $this->addFilter('entity_id', array_keys($items), 'IN');

                return $this;
            }

            return $this->getEmptyCollection();
        }

        return $this;
    }

    public function addCarrierNonDeletedFilter()
    {
        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_Vendor(),
            'carrier_id',
            new Application_Model_Entity_Entity(),
            'id',
            ['vendor_deleted' => 'deleted'],
            'vendor.carrier_id = entity_2.id'
        );

        $this->addFilter('entity_2.deleted', 0);

        return $this;
    }
}
