<?php

class Application_Model_Entity_Collection_Accounts_Reserve_Vendor extends Application_Model_Base_Collection
{
    public function _beforeLoad()
    {
        parent::_beforeLoad();

        // $this->addFieldsForSelect(
        //     new Application_Model_Entity_Accounts_Reserve_Vendor(),
        //     'reserve_account_id',
        //     new Application_Model_Entity_Accounts_Reserve(),
        //     'id',
        //     [
        //         'priority',
        //         'entity_id',
        //         'account_name',
        //         'description',
        //         'contribution_amount',
        //         'current_balance',
        //         'min_balance',
        //         'deleted',
        //     ]
        // );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Accounts_Reserve(),
            'entity_id',
            new Application_Model_Entity_Entity(),
            'id',
            ['entity_type_id', 'name']
        );

        return $this;
    }

    /**
     * Filters RA Vendor collection in accordance with current user
     *
     * @return Application_Model_Entity_Collection_Accounts_Reserve_Vendor
     */
    public function addVisibilityFilterForUser()
    {
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();

        if (!$userEntity->isAdminOrSuperAdmin()) {
            if ($userEntity->getUserRoleID() == Application_Model_Entity_System_UserRoles::ONBOARDING_ROLE_ID) {
                $entityId = Application_Model_Entity_Entity::getCurrentEntity()->getId();
            } else {
                $entityId = $userEntity->getEntity()->getEntityId();
            }

            $reserveAccountEntity = new Application_Model_Entity_Accounts_Reserve();
            $reserveAccountCollection = $reserveAccountEntity->getCollection();

            if ($userEntity->getUserRoleID() == Application_Model_Entity_System_UserRoles::ONBOARDING_ROLE_ID) {
                $reserveAccountCollection->addFilter('entity_id', $entityId);
            } else {
                $userVisibilityEntity = new Application_Model_Entity_Accounts_UsersVisibility();
                $participantIds = $userVisibilityEntity->getCollection()->addFilter('entity_id', $entityId)->getField(
                    'participant_id'
                );
                if ($participantIds == []) {
                    array_push($participantIds, '0');
                }
                $participantIds[] = $entityId;
                $reserveAccountCollection->addFilter('entity_id', $participantIds, 'IN');
            }

            $reserveAccountIds = $reserveAccountCollection->getField('id');
            if ($reserveAccountIds == []) {
                array_push($reserveAccountIds, '0');
            }
            $this->addFilter('reserve_account_id', $reserveAccountIds, 'IN');
        }

        return $this;
    }

    public function addVendorFilter()
    {
        return $this->addFilter(
            'entity_type_id',
            Application_Model_Entity_Entity_Type::TYPE_VENDOR
        );
    }

    public function addCarrierFilter()
    {
        return $this->addFilter(
            'entity_type_id',
            Application_Model_Entity_Entity_Type::TYPE_VENDOR
        );
    }

    public function addCarrierVendorFilter($checkCarrierPermission = false, $ignoreisOnboarding = false)
    {
        //TODO will be change in SUP-1170
        if ($checkCarrierPermission) {
            $reserveAccountVendorView = Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
                Application_Model_Entity_Entity_Permissions::RESERVE_ACCOUNT_VENDOR_VIEW
            );
            $reserveAccountCarrierView = Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
                Application_Model_Entity_Entity_Permissions::RESERVE_ACCOUNT_CARRIER_VIEW
            );
        } else {
            $reserveAccountVendorView = $reserveAccountCarrierView = true;
        }
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();
        if ($userEntity->isOnboarding() && !$ignoreisOnboarding) {
            $this->addFilter('entity_id', $userEntity->getEntity()->getEntityId(), '=');
        } else {
            $vendorIds = [];
            if ($reserveAccountVendorView) {
                if ($contractorEntity = $userEntity->getSelectedContractor()) {
                    $vendorIds = (new Application_Model_Entity_Entity_ContractorVendor())->getCollection()->addFilter(
                        'contractor_id',
                        $contractorEntity->getEntityId()
                    )->addFilter('status', 0)->getField('vendor_id');
                } else {
                    $vendorIds = (new Application_Model_Entity_Entity_Vendor())->getCollection()->addFilter(
                        'carrier_id',
                        $userEntity->getSelectedCarrier()->getEntityId()
                    )->getField('entity_id');
                }
            }
            if ($reserveAccountCarrierView) {
                $vendorIds[] = $userEntity->getSelectedCarrier()->getEntityId();
            }

            $this->addFilter('entity_id', $vendorIds, 'IN');
        }

        return $this;
    }

    public function getDeletedFieldName()
    {
        return 'reserve_account.deleted';
    }

    public function addFilterByEntity($entityId)
    {
        if ($entityId) {
            $this->addFilter(
                'entity_id',
                $entityId,
                '=',
                true,
                $type = self::WHERE_TYPE_AND
            );
        }

        return $this;
    }

    /**
     * @param $entityId
     * @return Application_Model_Entity_Collection_Accounts_Reserve_Vendor
     */
    public function addFilterByEntityId($entityId)
    {
        return $this->addFilterByEntity($entityId); //@TODO: rename method addFilterByEntity
    }
}
