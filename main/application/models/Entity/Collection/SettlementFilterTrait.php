<?php

trait Application_Model_Entity_Collection_SettlementFilterTrait
{
    /**
     * filter collection by settlement_cycle_id field
     *
     * @param $settlementCycleId
     * @return Application_Model_Base_Collection
     */
    public function addSettlementFilter($settlementCycleId = null)
    {
        if (!$settlementCycleId) {
            $settlementCycleId = Application_Model_Entity_Accounts_User::getCurrentUser()->getCurrentCycle()->getId();
        }
        $this->addFilter('settlement_cycle_id', $settlementCycleId);

        return $this;
    }
}
