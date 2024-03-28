<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_System_VendorStatus as VendorStatus;

class Application_Model_Entity_Collection_Accounts_Reserve_Contractor extends Application_Model_Base_Collection
{
    use Application_Model_Entity_Collection_ContractorEntityFilterTrait;

    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Application_Model_Entity_Accounts_Reserve_Contractor(),
            'reserve_account_id',
            new Application_Model_Entity_Accounts_Reserve(),
            'id',
            [
                'contractor_entity_id' => 'entity_id',
                'account_name',
                'description',
                'priority',
                'min_balance',
                'contribution_amount',
                'initial_balance',
                'current_balance',
                'starting_balance',
                'disbursement_code',
                'balance',
                'reserve_account_contractor_id' => 'id',
                'created_at',
                'accumulated_interest',
                'deleted',
            ]
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Accounts_Reserve(),
            'entity_id',
            new Application_Model_Entity_Entity(),
            'id',
            ['entity_type_id', 'name']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Accounts_Reserve(),
            'entity_id',
            new Contractor(),
            'entity_id',
            ['contractor_code' => 'code', 'contractor_status' => 'status']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Accounts_Reserve_Contractor(),
            'reserve_account_vendor_id',
            new Application_Model_Entity_Accounts_Reserve_Vendor(),
            'id',
            [
                'vendor_reserve_account_id' => 'reserve_account_id',
                /*'default_vendor_reserve_code' => 'vendor_reserve_code'*/
            ]
        );
        $this->addFieldsForSelect(
            new Application_Model_Entity_Accounts_Reserve_Vendor(),
            'reserve_account_id',
            new Application_Model_Entity_Accounts_Reserve(),
            'id',
            ['vendor_entity_id' => 'entity_id', 'rav_priority' => 'priority'],
            'reserve_account_vendor.reserve_account_id=reserve_account_2.id'
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Accounts_Reserve(),
            'entity_id',
            new Application_Model_Entity_Entity(),
            'id',
            ['vendor_entity_type_id' => 'entity_type_id', 'vendor_name' => 'name'],
            'reserve_account_2.entity_id=entity_2.id'
        );

        $this->addFieldsForSelect(
            new Contractor(),
            'entity_id',
            new Application_Model_Entity_Powerunit_Powerunit(),
            'contractor_id',
            ['powerunit_code' => 'code']
        );

        return $this;
    }

    /**
     * Filters RA Contractor collection in accordance with carrier_contractor table
     *
     * @return Application_Model_Entity_Collection_Accounts_Reserve_Contractor
     */
    public function addVisibilityFilterForUser()
    {
        $userEntity = User::getCurrentUser();

        if ($userEntity->getUserRoleID() != Application_Model_Entity_System_UserRoles::CONTRACTOR_ROLE_ID) {
            $entityId = $userEntity->getSelectedCarrier()->getEntityId();
            $contractorsId = (new Contractor())->getCollection()->addFilter(
                'carrier_id',
                $entityId,
                '='
            )->getField('entity_id');
            if (!(is_countable($contractorsId) ? count($contractorsId) : 0)) {
                $contractorsId = [0 => 0];
            }
            $this->addFilter('contractor_entity_id', $contractorsId, 'IN');
        } else {
            $this->addContractorFilter($userEntity->getEntityId());
        }

        $this->addContractorFilter();
        $this->vendorFilter();

        return $this;
    }

    /**
     * @return $this
     */
    public function vendorFilter()
    {
        $user = User::getCurrentUser();
        if ($user->isVendor()) {
            $this->addFilter('reserve_account_2.entity_id', $user->getEntityId());
            $collection = (new Application_Model_Entity_Entity_ContractorVendor())->getCollection()->addFilter(
                'vendor_id',
                $user->getEntityId()
            )->addFilter('status', [VendorStatus::STATUS_ACTIVE, VendorStatus::STATUS_RESCINDED], 'IN');
            $contractorIds = $collection->getField('contractor_id');
            if ($contractorIds) {
                $this->addFilter('contractor_entity_id', $contractorIds, 'IN');
            } else {
                $this->addFilter('id', -1);
            }
        }

        return $this;
    }

    public function getDeletedFieldName()
    {
        return 'reserve_account.deleted';
    }

    public function addUniqueFilter(Application_Model_Entity_Accounts_Reserve_Contractor $rac)
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

    public function addActiveVendorFilter(Contractor $contractor)
    {
        $vendorIds = $contractor->getActiveVendorIds();
        if ($vendorIds) {
            $this->addFilter('reserve_account_2.entity_id', $vendorIds, 'IN');
        } else {
            return $this->getEmptyCollection();
        }

        return $this;
    }

    //    protected function _applyFilters()
    //    {
    //        foreach ($this->_filters as $filter) {
    //            if ($filter->getData('field') === 'vendor_reserve_code') {
    //                $filter->setData('field', 'reserve_account_contractor.vendor_reserve_code');
    //            }
    //        }
    //        return parent::_applyFilters();
    //    }
}
