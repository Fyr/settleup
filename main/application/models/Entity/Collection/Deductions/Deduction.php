<?php

use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_System_ContractorStatus as ContractorStatus;

class Application_Model_Entity_Collection_Deductions_Deduction extends Application_Model_Base_Collection
{
    use Application_Model_Entity_Collection_SettlementFilterTrait;
    use Application_Model_Entity_Collection_ContractorFilterTrait;

    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Application_Model_Entity_Deductions_Deduction(),
            'powerunit_id',
            new Application_Model_Entity_Powerunit_Powerunit(),
            'id',
            ['powerunit_code' => 'code']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Powerunit_Powerunit(),
            'contractor_id',
            new Contractor(),
            'entity_id',
            ['company_name', 'contractor_code' => 'code', 'contractor_status' => 'status', 'division']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Deductions_Deduction(),
            'provider_id',
            new Application_Model_Entity_Entity(),
            'id',
            [
                'provider_name' => 'name',
                'provider_entity_type_id' => 'entity_type_id',
            ]
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Deductions_Deduction(),
            'setup_id',
            new Application_Model_Entity_Deductions_Setup(),
            'id',
            [
                'sdescription' => 'description',
                'srecurring' => 'recurring',
                'setup_deleted' => 'deleted',
            ]
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Deductions_Deduction(),
            'recurring',
            new Application_Model_Entity_System_RecurringTitle(),
            'id',
            [
                'recurring_title' => 'title',
            ]
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Deductions_Deduction(),
            'settlement_cycle_id',
            new Application_Model_Entity_Settlement_Cycle(),
            'id',
            [
                'settlement_cycle_status' => 'status_id',
                'cycle_disbursement_date' => 'disbursement_date',
                'cycle_start_date',
                'cycle_close_date',
            ]
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Deductions_Deduction(),
            'billing_cycle_id',
            new Application_Model_Entity_System_CyclePeriod(),
            'id',
            [
                'billing_title' => 'title',
            ]
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Deductions_Deduction(),
            'status',
            new Application_Model_Entity_System_PaymentStatus(),
            'id',
            ['status_title' => 'title']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Settlement_Cycle(),
            'status_id',
            new Application_Model_Entity_System_SettlementCycleStatus(),
            'id',
            ['settlement_status_title' => 'title']
        );

        return $this;
    }

    /**
     * Filters deductions collection by currently selected carrier
     *
     * @return Application_Model_Entity_Collection_Deductions_Deduction
     */
    public function addCarrierFilter($applyCarrierPermissions = false)
    {
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();
        $currentCycles = $userEntity->getEntity()->getCurrentCarrier()->getCycles()->getField('id');
        if ($currentCycles) {
            $this->addFilter('settlement_cycle_id', $currentCycles, 'IN');
        } else {
            $this->addFilter('settlement_cycle_id', ["0"], 'IN');
        }

        if (!$userEntity->hasPermission(Application_Model_Entity_Entity_Permissions::VENDOR_DEDUCTION_VIEW)) {
            $this->addFilter('provider_entity_type_id', Application_Model_Entity_Entity_Type::TYPE_VENDOR, '!=');
        }
        if ($userEntity->getUserRoleID() == Application_Model_Entity_System_UserRoles::VENDOR_ROLE_ID) {
            $this->addFilter('provider_id', $userEntity->getEntityId());
        }

        //        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();
        //        $carrierEntity = $userEntity->getSelectedCarrier();
        //        $this->addFilter
        return $this;
    }

    /**
     * return $amount - $adjustedBalance
     *
     * @return float
     */
    public function getAffectedAmount()
    {
        $amount = 0;
        $adjuctedBalance = 0;
        foreach ($this as $deduction) {
            $amount += $deduction->getAmount();
            $adjuctedBalance += $deduction->getAdjustedBalance();
        }

        return $amount - $adjuctedBalance;
    }

    public function addWithdrawals()
    {
        $this->addColumn(
            new Zend_Db_Expr(
                '(SELECT SUM(IFNULL(reserve_transaction.amount,0)) FROM reserve_transaction WHERE type = 2 AND reserve_transaction.deduction_id = deductions.id AND reserve_transaction.deleted = 0) as withdrawals_amount'
            )
        );

        return $this;
    }

    /**
     * @param $cycle
     * @return $this
     */
    public function addNonAppliedRecurringsFilter($cycle)
    {
        $this->addFilter('settlement_cycle_id', '', 'IS NULL', false);
        $this->addFilter('carrier_id', $cycle->getCarrierId());
        $this->addFilter('contractor_status', ContractorStatus::STATUS_ACTIVE);
        $this->addNonDeletedFilter();
        $this->addFilter('setup_deleted', 0);
        $this->addFilter('srecurring', 1);

        return $this;
    }

    public function addApprovedVendorFilter(Contractor $contractor)
    {
        $vendorIds = $contractor->getActiveVendorIds();

        if (count($vendorIds)) {
            $this->addFilter('provider_id', $vendorIds, 'IN');
        } else {
            return $this->getEmptyCollection();
        }

        return $this;
    }

    public function addVendorAcctField()
    {
        $this->addFieldsForSelect(
            new Application_Model_Entity_Deductions_Deduction(),
            'setup_id',
            new Application_Model_Entity_Entity_ContractorVendor(),
            'id',
            ['vendor_acct'],
            'contractor_vendor.contractor_id=deductions.contractor_id AND contractor_vendor.vendor_id = provider_id'
        );

        return $this;
    }
}
