<?php

class Application_Model_Entity_Collection_Transactions_Disbursement extends Application_Model_Base_Collection
{
    use Application_Model_Entity_Collection_SettlementFilterTrait;

    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Application_Model_Entity_Transactions_Disbursement(),
            'process_type',
            new Application_Model_Entity_System_DisbursementTransactionTypes(),
            'id',
            ['process_type_title' => 'title']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Transactions_Disbursement(),
            'entity_id',
            new Application_Model_Entity_Entity(),
            'id',
            ['name', 'entity_type_id']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Transactions_Disbursement(),
            'entity_id',
            new Application_Model_Entity_Entity_Contractor(),
            'entity_id',
            ['division']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Transactions_Disbursement(),
            'settlement_cycle_id',
            new Application_Model_Entity_Settlement_Cycle(),
            'id',
            ['disbursement_date', 'disbursement_status', 'cycle_start_date', 'cycle_close_date']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Settlement_Cycle(),
            'disbursement_status',
            new Application_Model_Entity_System_PaymentStatus(),
            'id',
            ['cycle_disbursement_status_title' => 'title']
        );
    }

    /**
     * Filters settlement cycle collection by currently selected carrier
     *
     * @return Application_Model_Entity_Collection_Settlement_Cycle
     */
    public function addCarrierFilter()
    {
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();
        /*$previousCycle = $userEntity
            ->getEntity()
            ->getCurrentCarrier()
            ->getPreviousSettlementCycle();
        $this->addFilter('settlement_cycle_id', $previousCycle->getId());*/
        $currentCycles = $userEntity->getEntity()->getCurrentCarrier()->getCycles()->getField('id');
        if ($currentCycles) {
            $this->addFilter('settlement_cycle_id', $currentCycles, 'IN');
        } else {
            $this->addFilter('settlement_cycle_id', ["0"], 'IN');
        }

        return $this;
    }

    public function approve($cycleId)
    {
        foreach ($this->getItems() as $disbursement) {
            $disbursement->approve();
        }
        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load($cycleId);
        $cycle->setDisbursementStatus(Application_Model_Entity_System_PaymentStatus::APPROVED_STATUS)->save();
    }
}
