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
            ->getItems();

        foreach ($deductions as $deduction) {
            $balance = ($deduction->getAdjustedBalance() ?: $deduction->getBalance()) - $deduction->getDeductionAmount();
            if (0 < $balance) {
                $holdDeduction = clone $deduction;
                $holdDeduction
                    ->setId(null)
                    ->setSettlementCycleId($cycle->getId())
                    ->setDeductionParentId($deduction->getId())
                    ->setBalance($balance)
                    ->setAdjustedBalance($balance)
                    ->setDeductionAmount(null)
                    ->setIsHold(true)
                    ->save();
            }
        }

        return $this;
    }
}
