<?php

use Application_Model_Entity_Deductions_Deduction as Deduction;
use Application_Model_Entity_Settlement_Cycle as Cycle;
use Application_Model_Entity_System_SettlementCycleStatus as CycleStatus;

trait Application_Model_Entity_Deductions_HoldTrait
{
    public function applyHoldDeductions(Cycle $cycle): self
    {
        if (CycleStatus::APPROVED_STATUS_ID === (int) $cycle->getStatusId()) {
            return $this;
        }

        $deductions = (new Deduction())
            ->getCollection()
            ->addNonDeletedFilter()
            ->addFilter('settlement_cycle_id', $cycle->getParentCycleId())
            ->addFilter('balance', 0, '>')
            ->getItems();

        foreach ($deductions as $deduction) {
            $holdDeduction = clone $deduction;
            $description =
                'Outstanding deduction of ' . $deduction->getBalance() . 'from Cycle #' . $cycle->getParentCycleId();
            $holdDeduction
                ->setId(null)
                ->setSettlementCycleId($cycle->getId())
                ->setDeductionParentId($deduction->getId())
                ->setAdjustedBalance($deduction->getBalance())
                ->setBalance($deduction->getBalance())
                ->setAmount(0)
                ->setIsHold(true)
                ->setDescription($description)
                ->save();
        }

        return $this;
    }
}
