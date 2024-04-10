<?php

class Application_Model_Entity_Collection_Entity_Contractor extends Application_Model_Base_Collection
{
    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_Contractor(),
            'status',
            new Application_Model_Entity_System_ContractorStatus(),
            'id',
            ['status_title' => 'title']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_Contractor(),
            'correspondence_method',
            new Application_Model_Entity_Entity_Contact_Type(),
            'id',
            ['correspondence_method_title' => 'title']
        );

        return $this;
    }

    /**
     * Adds visibility filter for current user
     *
     * @return Application_Model_Entity_Collection_Entity_Contractor
     */
    public function addVisibilityFilterForUser($showAllForAdmin = false)
    {
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();

        if (!$userEntity->isAdminOrSuperAdmin()) {
            $this->addVisibilityFilter(
                Application_Model_Entity_Entity::getCurrentEntity()->getId()
            );
        } else {
            if (!$showAllForAdmin) {
                $this->addFilterByCarrierContractor();
            }
        }

        return $this;
    }

    public function addVisibilityFilterForCurrentCarrier()
    {
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();
        $this->addVisibilityFilter(
            $userEntity->getEntity()->getEntityId()
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function addConfiguredFilter()
    {
        $this->addFilter('status', Application_Model_Entity_System_ContractorStatus::STATUS_NOT_CONFIGURED, '!=');

        return $this;
    }

    /**
     * Filters contractors collection in accordance with list of visibility
     *
     * @param int $userEntityId - Entity id of selected user
     * @return Application_Model_Entity_Collection_Entity_Contractor
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
     * Filters contractors collection in accordance with list of visibility
     * except currently added items
     *
     * @param int $userEntityId - Entity id of selected user
     * @return Application_Model_Entity_Collection_Entity_Contractor
     */
    public function addFilterByAddedEntities($userEntityId)
    {
        $contractorEntity = new Application_Model_Entity_Entity_Contractor();
        $entitiesId = $contractorEntity->getCollection()->addVisibilityFilter($userEntityId)->getField('entity_id');
        if ($entitiesId == []) {
            return $this;
        } else {
            $this->addFilter('entity_id', $entitiesId, 'NOT IN');
        }

        return $this;
    }

    public function addFilterByVendorVisibility($onlyActive = true)
    {
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();
        if ($userEntity->isOnboarding()) {
            $entity = $userEntity->getEntity();
            if ($onlyActive) {
                $status = [
                    Application_Model_Entity_System_VendorStatus::STATUS_ACTIVE,
                ];
            } else {
                $status = [
                    Application_Model_Entity_System_VendorStatus::STATUS_ACTIVE,
                    Application_Model_Entity_System_VendorStatus::STATUS_RESCINDED,
                ];
            }
            $contractorsId = (new Application_Model_Entity_Entity_ContractorVendor())->getCollection()->addFilter(
                'vendor_id',
                $entity->getEntityId()
            )->addFilter(
                'status',
                $status,
                'IN'
            )->getField('contractor_id');
            if (!$contractorsId) {
                $contractorsId = [0];
            }
            $this->addFilter('entity_id', $contractorsId, 'IN');
        }

        return $this;
    }

    public function vendorFilter()
    {
        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        if ($user->isOnboarding()) {
            $this->addFieldsForSelect(
                new Application_Model_Entity_Entity_Contractor(),
                'entity_id',
                new Application_Model_Entity_Entity_ContractorVendor(),
                'contractor_id',
                ['vendor_id', 'vendor_status' => 'status'],
                null,
                true
            );
            $this->addFieldsForSelect(
                new Application_Model_Entity_Entity_ContractorVendor(),
                'status',
                new Application_Model_Entity_System_VendorStatus(),
                'id',
                ['vendor_status_title' => 'title']
            );
            $this->addFilter('vendor_id', $user->getEntityId());

            $status = [
                Application_Model_Entity_System_VendorStatus::STATUS_ACTIVE,
                Application_Model_Entity_System_VendorStatus::STATUS_RESCINDED,
            ];
            $this->addFilter(
                'status',
                $status,
                'IN'
            );
        }

        return $this;
    }

    public function addVendorStatusFilter($vendorId, $vendorStatus = null)
    {
        if (Application_Model_Entity_Entity::staticLoad($vendorId)->getEntityTypeId(
        ) == Application_Model_Entity_Entity_Type::TYPE_VENDOR) {
            $this->addFieldsForSelect(
                new Application_Model_Entity_Entity_Contractor(),
                'entity_id',
                new Application_Model_Entity_Entity_ContractorVendor(),
                'contractor_id',
                ['vendor_id', 'vendor_status' => 'status'],
                null,
                true
            );
            $this->addFieldsForSelect(
                new Application_Model_Entity_Entity_ContractorVendor(),
                'status',
                new Application_Model_Entity_System_VendorStatus(),
                'id',
                ['vendor_status_title' => 'title']
            );
            $this->addFieldsForSelect(
                new Application_Model_Entity_Entity_ContractorVendor(),
                'vendor_id',
                new Application_Model_Entity_Entity_Vendor(),
                'entity_id',
                ['vendor_name' => 'name']
            );

            $this->addFilter('vendor_id', $vendorId);

            if ($vendorStatus !== null) {
                $this->addFilter('vendor_status', (int)$vendorStatus);
            }
        } else {
            $this->addFieldsForSelect(
                new Application_Model_Entity_Entity_Contractor(),
                'carrier_status_id',
                new Application_Model_Entity_System_VendorStatus(),
                'id',
                ['vendor_status_title' => 'title']
            );
            $this->addFieldsForSelect(
                new Application_Model_Entity_Entity_Contractor(),
                'carrier_id',
                new Application_Model_Entity_Entity_Carrier(),
                'entity_id',
                ['vendor_name' => 'name']
            );

            $this->addFilter('carrier_id', $vendorId);

            if ($vendorStatus !== null) {
                $this->addFilter('carrier_status_id', (int)$vendorStatus);
            }
        }

        return $this;
    }

    public function addFilterByCarrierContractor($carrierId = null)
    {
        if (!$carrierId) {
            $carrier = Application_Model_Entity_Accounts_User::getCurrentUser()->getSelectedCarrier();
            if ($carrier->getId()) {
                $carrierId = $carrier->getEntityId();
            } else {
                return $this->getEmptyCollection();
            }
        }

        $this->addFilter('carrier_id', $carrierId);

        return $this;
    }

    public function addFilterByActiveCarrierContractor($carrierId = null)
    {
        if (!$carrierId) {
            $carrier = Application_Model_Entity_Accounts_User::getCurrentUser()->getSelectedCarrier();
            if ($carrier->getId()) {
                $carrierId = $carrier->getEntityId();
            } else {
                return $this->getEmptyCollection();
            }
        }

        $this->addFilter('carrier_id', $carrierId);
        $this->addFilter('status', Application_Model_Entity_System_ContractorStatus::STATUS_ACTIVE);

        return $this;
    }

    /**
     * Filters payments collection by currently selected carrier
     *
     * @return Application_Model_Entity_Collection_Payments_Payment
     */
    public function addCarrierFilter()
    {
        $carrierEntity = Application_Model_Entity_Accounts_User::getCurrentUser()->getEntity();
        $this->addFilter('carrier_id', $carrierEntity->getEntityId());

        return $this;
    }

    public function addCarrierName()
    {
        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_Contractor(),
            'carrier_id',
            new Application_Model_Entity_Entity_Carrier(),
            'entity_id',
            ['carrier_name' => 'name']
        );

        return $this;
    }

    public function getDeletedFieldName()
    {
        return 'entity.deleted';
    }

    public function addFilterByCorrespondenceMethod($correspondenceMethod)
    {
        $this->addFilter('correspondence_method', $correspondenceMethod);

        return $this;
    }

    public function addFilterByStatus($status = 0)
    {
        $status = (int)$status;
        if ($status != 0) {
            $this->addFilter('contractor.status', $status);
        }

        return $this;
    }

    public function addCarrierNonDeletedFilter()
    {
        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_Contractor(),
            'carrier_id',
            new Application_Model_Entity_Entity(),
            'id',
            ['carrier_deleted' => 'deleted'],
            'contractor.carrier_id = entity_2.id'
        );

        $this->addFilter('entity_2.deleted', 0);

        return $this;
    }

    public function addSettlementGroup(): self
    {
        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_Contractor(),
            'settlement_group_id',
            new Application_Model_Entity_Settlement_Group(),
            'id',
            ['settlement_group' => 'code']
        );

        return $this;
    }

    public function addSettlementGroupFilter()
    {
        $currentSettlementGroupId = Application_Model_Entity_Accounts_User::getCurrentUser()->getLastSelectedSettlementGroup();
        $this->addFilter('settlement_group_id', $currentSettlementGroupId);

        return $this;
    }

    public function addContractorVendorFilter(): self
    {
        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_Contractor(),
            'entity_id',
            new Application_Model_Entity_Entity_ContractorVendor(),
            'contractor_id',
            ['vendor_id', 'vendor_status' => 'status'],
            null,
            true
        );
        $this->addFilter('vendor_status', Application_Model_Entity_System_VendorStatus::STATUS_ACTIVE);

        return $this;
    }
}
