<?php

trait Application_Model_Entity_SettlementCycleString
{
    public function getSettlementCycleString()
    {
        if (!$this->getData('settlement_cycle_string')) {
            $startDate = DateTime::createFromFormat('Y-m-d', $this->getCycleStartDate())->format('n/j/Y');
            $closeDate = DateTime::createFromFormat('Y-m-d', $this->getCycleCloseDate())->format('n/j/Y');
            $this->setData('settlement_cycle_string', $startDate . ' - ' . $closeDate);
        }

        return $this->getData('settlement_cycle_string');
    }
}
