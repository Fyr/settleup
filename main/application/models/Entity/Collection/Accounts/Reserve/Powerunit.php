<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Powerunit_Powerunit as Powerunit;
use Application_Model_Entity_System_VendorStatus as VendorStatus;

class Application_Model_Entity_Collection_Accounts_Reserve_Powerunit extends Application_Model_Base_Collection
{
    use Application_Model_Entity_Collection_ContractorEntityFilterTrait;

    public function _beforeLoad()
    {
        parent::_beforeLoad();

        // $this->addFieldsForSelect(
        //     new Application_Model_Entity_Accounts_Reserve_Powerunit(),
        //     'id',
        //     new Application_Model_Entity_Accounts_Reserve(),
        //     'id',
        //     [
        //         'contractor_entity_id' => 'entity_id',
        //         'account_name',
        //         'description',
        //         'priority',
        //         'min_balance',
        //         'contribution_amount',
        //         'initial_balance',
        //         'current_balance',
        //         'starting_balance',
        //         'disbursement_code',
        //         'balance',
        //         'reserve_account_id' => 'id',
        //         'created_at',
        //         'accumulated_interest',
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

        $this->addFieldsForSelect(
            new Application_Model_Entity_Accounts_Reserve(),
            'powerunit_id',
            new Powerunit(),
            'id',
            ['powerunit_code' => 'code', 'powerunit_status' => 'status']
        );

        // $this->addFieldsForSelect(
        //     new Application_Model_Entity_Accounts_Reserve_Powerunit(),
        //     'reserve_account_vendor_id',
        //     new Application_Model_Entity_Accounts_Reserve_Vendor(),
        //     'id',
        //     [
        //         'vendor_reserve_account_id' => 'reserve_account_id',
        //         /*'default_vendor_reserve_code' => 'vendor_reserve_code'*/
        //     ]
        // );
        // $this->addFieldsForSelect(
        //     new Application_Model_Entity_Accounts_Reserve_Vendor(),
        //     'reserve_account_id',
        //     new Application_Model_Entity_Accounts_Reserve(),
        //     'id',
        //     ['vendor_entity_id' => 'entity_id', 'rav_priority' => 'priority'],
        //     'reserve_account_vendor.reserve_account_id=reserve_account_2.id'
        // );

        // $this->addFieldsForSelect(
        //     new Application_Model_Entity_Accounts_Reserve(),
        //     'entity_id',
        //     new Application_Model_Entity_Entity(),
        //     'id',
        //     ['vendor_entity_type_id' => 'entity_type_id', 'vendor_name' => 'name'],
        //     'reserve_account_2.entity_id=entity_2.id'
        // );

        // $this->addFieldsForSelect(
        //     new Powerunit(),
        //     'entity_id',
        //     new Application_Model_Entity_Powerunit_Powerunit(),
        //     'powerunit_id',
        //     ['powerunit_code' => 'code']
        // );

        return $this;
    }

    /**
     * Filters RA Powerunit collection in accordance with carrier_powerunit table
     *
     * @return Application_Model_Entity_Collection_Accounts_Reserve_Powerunit
     */
    public function addVisibilityFilterForUser()
    {
        $userEntity = User::getCurrentUser();

        if ($userEntity->getUserRoleID() != Application_Model_Entity_System_UserRoles::ONBOARDING_ROLE_ID) {
            $entityId = $userEntity->getSelectedCarrier()->getEntityId();
            $powerunitsId = (new Powerunit())->getCollection()->addFilter(
                'carrier_id',
                $entityId,
                '='
            )->getField('id');
            if (!(is_countable($powerunitsId) ? count($powerunitsId) : 0)) {
                $powerunitsId = [0 => 0];
            }
            // $this->addFilter('contractor_entity_id', $powerunitsId, 'IN');
        } else {
            // $this->addContractorFilter($userEntity->getEntityId());
        }

        // $this->addContractorFilter();
        // $this->vendorFilter();

        return $this;
    }

    /**
     * @return $this
     */
    // public function vendorFilter()
    // {
    //     $user = User::getCurrentUser();
    //     if ($user->isSpecialist()) {
    //         $this->addFilter('reserve_account_2.entity_id', $user->getEntityId());
    //         $collection = (new Application_Model_Entity_Powerunit_PowerunitVendor())->getCollection()->addFilter(
    //             'vendor_id',
    //             $user->getEntityId()
    //         )->addFilter('status', [VendorStatus::STATUS_ACTIVE, VendorStatus::STATUS_RESCINDED], 'IN');
    //         $powerunitIds = $collection->getField('powerunit_id');
    //         if ($powerunitIds) {
    //             $this->addFilter('contractor_entity_id', $powerunitIds, 'IN');
    //         } else {
    //             $this->addFilter('id', -1);
    //         }
    //     }

    //     return $this;
    // }

    public function getDeletedFieldName()
    {
        return 'reserve_account.deleted';
    }

    public function addUniqueFilter(Application_Model_Entity_Accounts_Reserve_Powerunit $rac)
    {
        if ($id = $rac->getId()) {
            $this->addFilter('id', $id, '!=');
        }
        if (($ravId = $rac->getReserveAccountVendorId()) && ($code = $rac->getVendorReserveCode(
        )) && ($entityId = $rac->getEntityId())) {
            $this->addFilter('reserve_account_vendor_id', $ravId);
            $this->addFilter('vendor_reserve_code', $code);
            $this->addFilter('entity_id', $entityId);
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

    // public function addActiveVendorFilter(Powerunit $powerunit)
    // {
    //     $vendorIds = $powerunit->getActiveVendorIds();
    //     if ($vendorIds) {
    //         $this->addFilter('reserve_account_2.entity_id', $vendorIds, 'IN');
    //     } else {
    //         return $this->getEmptyCollection();
    //     }

    //     return $this;
    // }

    //    protected function _applyFilters()
    //    {
    //        foreach ($this->_filters as $filter) {
    //            if ($filter->getData('field') === 'vendor_reserve_code') {
    //                $filter->setData('field', 'reserve_account.vendor_reserve_code');
    //            }
    //        }
    //        return parent::_applyFilters();
    //    }

    public function addPowerunitFilter($powerunitId)
    {
        $this->addFilter(
            'reserve_account.powerunit_id',
            $powerunitId,
        );

        return $this;
    }

    public function addMaintenanceFilter()
    {
        $this->addFilter(
            'reserve_account.account_type',
            Application_Model_Entity_System_ReserveAccountType::MAINTENANCE_ACCOUNT,
        );

        return $this;
    }
}
