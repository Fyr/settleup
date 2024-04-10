<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Deductions_Setup as Setup;
use Application_Model_Entity_Entity as Entity;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Entity_ContractorVendor as ContractorVendor;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_Entity_Vendor as Vendor;
use Application_Model_Entity_Powerunit_Powerunit as Powerunit;
use Application_Model_Entity_System_CyclePeriod as CyclePeriod;
use Application_Model_Entity_System_RecurringTitle as RecurringTitle;
use Application_Model_Entity_System_SetupLevels as SetupLevels;
use Application_Model_Entity_System_VendorStatus as VendorStatus;

class Application_Model_Entity_Collection_Deductions_Setup extends Application_Model_Base_Collection
{
    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Setup(),
            'recurring',
            new RecurringTitle(),
            'id',
            ['recurring_title' => 'title']
        );

        $this->addFieldsForSelect(
            new Setup(),
            'provider_id',
            new Entity(),
            'id',
            ['provider_name' => 'name']
        );

        $this->addFieldsForSelect(
            new Setup(),
            'level_id',
            new SetupLevels(),
            'id',
            ['level_title' => 'title']
        );

        $this->addFieldsForSelect(
            new Setup(),
            'billing_cycle_id',
            new CyclePeriod(),
            'id',
            ['billing_title' => 'title']
        );

        $this->addFieldsForSelect(
            new Setup(),
            'contractor_id',
            new Contractor(),
            'entity_id',
            ['contractor_name' => 'company_name']
        );

        $this->addFieldsForSelect(
            new Setup(),
            'powerunit_id',
            new Powerunit(),
            'id',
            ['power_unit_code' => 'code']
        );

        return $this;
    }

    /**
     * Filters deductions setup collection by currently selected carrier
     *
     * @return Application_Model_Entity_Collection_Deductions_Setup
     */
    public function addCarrierFilter()
    {
        //todo fix filter for admin and vendor
        $userEntity = User::getCurrentUser();
        if ($userEntity->getUserRoleID() == Application_Model_Entity_System_UserRoles::ONBOARDING_ROLE_ID) {
            $this->addFilter(
                'provider_id',
                Entity::getCurrentEntity()->getId()
            );
        } else {
            if (!$userEntity->isAdminOrSuperAdmin()) {
                $userEntityId = Entity::getCurrentEntity()->getId();
            } else {
                $userEntityId = $userEntity->getEntity()->getEntityId();
            }
            $userVisibilityEntity = new Application_Model_Entity_Accounts_UsersVisibility();
            $participantIds = $userVisibilityEntity->getCollection()->addFilter('entity_id', $userEntityId)->getField(
                'participant_id'
            );
            if ($participantIds == []) {
                array_push($participantIds, '0');
            }

            $this->addFilter(
                'provider_id',
                $participantIds,
                'IN',
                true,
                Application_Model_Base_Collection::WHERE_TYPE_OR,
                true
            );
            $this->addFilter(
                'provider_id',
                $userEntity->getEntity()->getEntityId(),
                '=',
                true,
                Application_Model_Base_Collection::WHERE_TYPE_OR,
                true
            );
        }

        return $this;
    }

    public function addProviderIdFilter(?int $entityId): self
    {
        $entityId = $entityId ?: User::getCurrentUser()->getEntity()->getEntityId();
        $contractorVendorIds = (new Contractor())
            ->getCollection()
            ->addNonDeletedFilter()
            ->addContractorVendorFilter()
            ->addFilter('carrier_id', $entityId)
            ->getField('vendor_id');

        $contractorVendorIds[] = $entityId;
        $this->addFilter('provider_id', array_unique($contractorVendorIds), 'IN');

        return $this;
    }

    public function addUserVisibilityFilter($checkCarrierPermissions = [false, false], $useCarrier = false)
    {
        $user = User::getCurrentUser();

        if ($user->isOnboarding() && !$useCarrier) {
            if (($checkCarrierPermissions[0] && !$user->hasPermission(
                Permissions::VENDOR_DEDUCTION_VIEW
            )) || ($checkCarrierPermissions[1] && !$user->hasPermission(
                Permissions::VENDOR_DEDUCTION_MANAGE
            ))) {
                $this->addFilter('provider_id', 0);
            } else {
                $this->addFilter('provider_id', $user->getEntity()->getEntityId());
            }
        } else {
            if (($checkCarrierPermissions[0] && !$user->hasPermission(
                Permissions::VENDOR_DEDUCTION_VIEW
            )) || ($checkCarrierPermissions[1] && !$user->hasPermission(
                Permissions::VENDOR_DEDUCTION_MANAGE
            ))) {
                $vendorsId = [];
            } else {
                $vendorsId = (new Vendor())->getCollection()->addFilter(
                    'carrier_id',
                    $user->getSelectedCarrier()->getEntityId()
                )->getField('entity_id');
            }

            $vendorsId[] = $user->getSelectedCarrier()->getEntityId();
            $this->addFilter('provider_id', $vendorsId, 'IN');
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addMasterFilter()
    {
        $this->addFilter('level_id', SetupLevels::MASTER_LEVEL_ID);

        return $this;
    }

    public function addFilterByEntityId($contractorId = null)
    {
        $this->addFilter('contractor_id', $contractorId);
        $this->providerFilter($contractorId);

        return $this;
    }

    /**
     * @param $contractorId
     * @return array
     */
    public function providerFilter($contractorId)
    {
        $contractorVendor = new ContractorVendor();
        $ids = $contractorVendor->getCollection()->filterByContractor($contractorId)->addFilter(
            'status',
            [VendorStatus::STATUS_ACTIVE, VendorStatus::STATUS_RESCINDED],
            'IN'
        )->getField('vendor_id');

        $contractor = Contractor::staticLoad($contractorId, 'entity_id');
        if ($contractor->getId() && in_array(
            $contractor->getCarrierStatusId(),
            [VendorStatus::STATUS_ACTIVE, VendorStatus::STATUS_RESCINDED]
        )) {
            $ids[] = $contractor->getCarrierId();
        }

        if (is_countable($ids) ? count($ids) : 0) {
            return $this->addFilter('provider_id', $ids, 'IN');
        }

        return $this->getEmptyCollection();
    }
}
