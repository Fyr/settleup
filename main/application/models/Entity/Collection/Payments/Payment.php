<?php

use Application_Model_Entity_System_ContractorStatus as ContractorStatus;

class Application_Model_Entity_Collection_Payments_Payment extends Application_Model_Base_Collection
{
    use Application_Model_Entity_Collection_SettlementFilterTrait;
    use Application_Model_Entity_Collection_ContractorFilterTrait;

    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Application_Model_Entity_Payments_Payment(),
            'powerunit_id',
            new Application_Model_Entity_Powerunit_Powerunit(),
            'id',
            ['powerunit_code' => 'code']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Powerunit_Powerunit(),
            'contractor_id',
            new Application_Model_Entity_Entity_Contractor(),
            'entity_id',
            ['company_name', 'contractor_code' => 'code', 'contractor_status' => 'status', 'division']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Payments_Payment(),
            'setup_id',
            new Application_Model_Entity_Payments_Setup(),
            'id',
            [
                'sdescription' => 'description',
                'scarrier_id' => 'carrier_id',
                'srecurring' => 'recurring',
                'setup_deleted' => 'deleted',
            ]
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Payments_Payment(),
            'carrier_id',
            new Application_Model_Entity_Entity_Carrier(),
            'entity_id',
            ['scarrier_name' => 'name']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Payments_Payment(),
            'recurring',
            new Application_Model_Entity_System_RecurringTitle(),
            'id',
            ['recurring_title' => 'title']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Payments_Payment(),
            'billing_cycle_id',
            new Application_Model_Entity_System_CyclePeriod(),
            'id',
            ['billing_title' => 'title']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Payments_Payment(),
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
            new Application_Model_Entity_Payments_Payment(),
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
     * Filters payments collection by currently selected carrier
     *
     * @return Application_Model_Entity_Collection_Payments_Payment
     */
    public function addCarrierFilter()
    {
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();
        $currentCycles = $userEntity->getEntity()->getCycles()->getField('id');
        if ($currentCycles) {
            $this->addFilter('settlement_cycle_id', $currentCycles, 'IN');
        } else {
            $this->addFilter('settlement_cycle_id', ["0"], 'IN');
        }

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

    /**
     * return amount
     *
     * @return float
     */
    public function getAmount()
    {
        $amount = 0;
        foreach ($this as $payment) {
            $amount += $payment->getAmount();
        }

        return $amount;
    }
}
