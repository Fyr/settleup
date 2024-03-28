<?php

trait Application_Model_Entity_CycleDataTrait
{
    protected $_cycle;

    /**
     * @return $this
     */
    public function appendCycleData()
    {
        $cycle = $this->_getSettlementCycle();
        if ($cycle->getId()) {
            foreach ($this->getCycleFields() as $fromField => $toField) {
                $this->setData($toField, $cycle->getData($fromField));
            }
        }

        return $this;
    }

    /**
     * @return Application_Model_Entity_Settlement_Cycle
     */
    protected function _getSettlementCycle()
    {
        if (isset($this->_cycle)) {
            return $this->_cycle;
        } else {
            $cycle = new Application_Model_Entity_Settlement_Cycle();
            if ($cycleId = $this->getSettlementCycleId()) {
                return $this->_cycle = $cycle->load($cycleId);
            }

            return $cycle;
        }
    }

    protected function getCycleFields()
    {
        return ['status_id' => 'status', 'approved_by' => 'approved_by', 'approved_datetime' => 'approved_datetime'];
    }
}
