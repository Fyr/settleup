<?php

trait Application_Model_Entity_SettlementCycleTrait
{
    protected $cycle;

    /**
     * @return Application_Model_Entity_Settlement_Cycle
     */
    public function getSettlementCycle()
    {
        if (isset($this->cycle) && $this->getSettlementCycleId() == $this->cycle->getId()) {
            return $this->cycle;
        } else {
            $cycle = new Application_Model_Entity_Settlement_Cycle();
            $this->cycle = $cycle->load($this->getSettlementCycleId());

            return $this->cycle;
        }
    }
}
