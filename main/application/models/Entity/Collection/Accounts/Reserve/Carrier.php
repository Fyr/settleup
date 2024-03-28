<?php

class Application_Model_Entity_Collection_Accounts_Reserve_Carrier extends Application_Model_Base_Collection
{
    use Application_Model_Entity_Collection_ContractorEntityFilterTrait;

    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Application_Model_Entity_Accounts_Reserve_Carrier(),
            'reserve_account_id',
            new Application_Model_Entity_Accounts_Reserve(),
            'id',
            [
                'entity_id',
                'account_name',
                'description',
                'priority',
                'min_balance',
                'contribution_amount',
                'initial_balance',
                'current_balance',
                'disbursement_code',
                'balance',
            ]
        );

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
     * Filters RA Contractor collection in accordance with user visibility list
     *
     * @return Application_Model_Entity_Collection_Accounts_Reserve_Carrier
     */
    public function addVisibilityFilterForUser()
    {
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();

        if (!$userEntity->isAdmin()) {
            if ($userEntity->getUserRoleID() == Application_Model_Entity_System_UserRoles::CARRIER_ROLE_ID) {
                $entityId = Application_Model_Entity_Entity::getCurrentEntity()->getId();
            } else {
                $entityId = $userEntity->getEntity()->getCurrentCarrier()->getEntityId();
            }

            $userVisibilityEntity = new Application_Model_Entity_Accounts_UsersVisibility();
            $participantIds = $userVisibilityEntity->getCollection()->addFilter(
                'entity_id',
                $entityId
            )->getField('participant_id');
            if ($participantIds == []) {
                array_push($participantIds, '0');
            }
            $participantIds[] = $entityId;
            $reserveAccountEntity = new Application_Model_Entity_Accounts_Reserve();
            $reserveAccountCollection = $reserveAccountEntity->getCollection();

            if ($userEntity->getUserRoleID() == Application_Model_Entity_System_UserRoles::CONTRACTOR_ROLE_ID) {
                $reserveAccountCollection->addFilter('entity_id', $entityId);
            } else {
                $reserveAccountCollection->addFilter(
                    'entity_id',
                    $participantIds,
                    'IN'
                );
            }

            $reserveAccountIds = $reserveAccountCollection->getField('id');
            if ($reserveAccountIds == []) {
                array_push($reserveAccountIds, '0');
            }
            $this->addFilter('reserve_account_id', $reserveAccountIds, 'IN');
        }

        return $this;
    }

    public function addFilterByEntityId($entityId)
    {
        $this->addFilter(
            'reserve_account.entity_id',
            $entityId,
            '=',
            true,
            $type = Application_Model_Base_Collection::WHERE_TYPE_AND
        );
    }
}
