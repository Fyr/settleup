<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Deductions_Deduction as Deduction;
use Application_Model_Entity_Payments_Payment as Payment;
use Application_Model_Entity_Settlement_Cycle as Cycle;
use Application_Model_Entity_System_CyclePeriod as CyclePeriod;
use Application_Model_Entity_System_SettlementCycleStatus as CycleStatus;
use Application_Model_Recurring_BiWeeklyStrategy as BiWeeklyStrategy;
use Application_Model_Recurring_MonthlyStrategy as MonthlyStrategy;
use Application_Model_Recurring_SemiMonthlyStrategy as SemiMonthlyStrategy;
use Application_Model_Recurring_SemiWeeklyStrategy as SemiWeeklyStrategy;
use Application_Model_Recurring_WeeklyStrategy as WeeklyStrategy;

trait Application_Model_Recurring_RecurringStrategyTrait
{
    public function recurring()
    {
        $this->setDefaultInvoiceDueDate();
        $this->setAddedInCycle($this->getSettlementCycleId());
        $this->unsSettlementCycleId();

        return $this;
    }

    protected function getNextInvoiceDate($date = null, $first = true)
    {
        $result = $this->getRecurringStrategy()?->getInvoiceDate($date, $first);
        if (!$result) {
            throw new Exception('Invalid Invoice Date on ' . $this->getSetupId() . ' setup');
        }

        return $result;
    }

    public function applyRecurrings(Cycle $cycle)
    {
        if ($cycle->getStatusId() == CycleStatus::APPROVED_STATUS_ID) {
            return $this;
        }
        $recurrings = $this->getNonAppliedRecurrings($cycle);
        if ($recurrings->count()) {
            foreach ($recurrings as $recurring) {
                /** @var Payment | Deduction $recurring */
                if ($recurring->isValidRecurring()) {
                    $entity = clone $recurring;
                    $entity->setRecurringParentId($entity->getId());
                    $entity->applyRecurring($cycle);
                }
            }
            //            $this->deleteNotAppliedRecurrings(array_keys($recurrings->getItems()));
        }

        return $this;
    }

    abstract public function isValidRecurring();

    /**
     * @return mixed
     */
    public function getNonAppliedRecurrings(Cycle $cycle)
    {
        /** @var Application_Model_Entity_Collection_Payments_Payment | Application_Model_Entity_Collection_Deductions_Deduction $collection */
        return $this->getCollection()->addNonAppliedRecurringsFilter($cycle);
    }

    //    /**
    //     * @param array $ids
    //     * @return $this
    //     */
    //    public function deleteNotAppliedRecurrings(array $ids)
    //    {
    //        if (count($ids)) {
    //            $this->getResource()->update(array('deleted' => 1), array('id IN (?)' => $ids));
    //        }
    //        return $this;
    //    }
    /**
     * @return $this
     */
    public function applyRecurring(Cycle $cycle)
    {
        $startDate = max($this->getInvoiceDate(), $cycle->getCycleStartDate());
        if ($startDate > $cycle->getCycleCloseDate()) {
            return $this;
        }
        $this->getRecurringDataFromSetup();
        $this->getDataFromSetup();
        for (
            $invoiceDate = $this->getNextInvoiceDate($startDate);
            $cycle->inCycle(
                $invoiceDate
            ); $invoiceDate = $this->getNextInvoiceDate($invoiceDate, false)
        ) {
            $this->setInvoiceDate($invoiceDate);
            $this->setSettlementCycleId($cycle->getId());
            $this->unsId();
            $this->setDefaultInvoiceDueDate();
            $this->setDefaultDisbursementDate();
            $this->setCreatedDatetime((new DateTime())->format('Y-m-d'));
            $this->save();
            if ($this->getSourceId()) {
                $this->setSourceId();
            }
            $invoiceDate = Datetime::createFromFormat('Y-m-d', $invoiceDate)->modify('+1 day')->format('Y-m-d');
        }

        return $this;
    }

    public function applyRecurringAsRegular($cycle)
    {
        $this->setSettlementCycleId($cycle->getId());
        $this->unsId();
        $this->save();
    }

    /**
     * @return Application_Model_Recurring_IRecurringStrategy|null
     */
    public function getRecurringStrategy()
    {
        return match ((int)$this->getBillingCycleId()) {
            CyclePeriod::WEEKLY_PERIOD_ID => new WeeklyStrategy($this),
            CyclePeriod::BIWEEKLY_PERIOD_ID => new BiWeeklyStrategy($this),
            CyclePeriod::MONTHLY_PERIOD_ID => new MonthlyStrategy($this),
            CyclePeriod::SEMY_MONTHLY_PERIOD_ID => new SemiMonthlyStrategy($this),
            CyclePeriod::MONTHLY_SEMI_MONTHLY_ID => new MonthlyStrategy($this),
            CyclePeriod::SEMI_WEEKLY_PERIOD_ID => new SemiWeeklyStrategy($this),
            default => null,
        };
    }

    public function clear(Cycle $cycle)
    {
        /** @var Application_Model_Base_Resource $resource */
        $resource = $this->getResource();

        // 1) Delete all entities in cycle
        $resource->update(['deleted' => 1], [
            'settlement_cycle_id = ?' => $cycle->getId(),
        ]);

        // 2) Deleted recurrings added in this cycle
        $resource->update(['deleted' => 1], [
            'added_in_cycle = ?' => $cycle->getId(),
            'settlement_cycle_id IS NULL',
        ]);

        // 2) Restore old recurrings
        $resource->update(['deleted' => 0], [
            'settlement_cycle_id IS NULL',
            'deleted_in_cycle = ?' => $cycle->getId(),
        ]);

        return $this;
    }

    public function delete($recurring = false)
    {
        if ($recurring) {
            if ($this->getRecurring()) {
                $this->deleteRecurrings($this->getRecurringParentId(), $this->getInvoiceDate());
            }
        }
        $this->setDeleted(1);
        $this->save();

        return $this;
    }

    public function deleteRecurrings($recurringParentId, $date)
    {
        $this->getResource()->update(
            ['deleted' => 1],
            ['recurring_parent_id = ?' => $recurringParentId, 'invoice_date >= ?' => $date]
        );
        $this->getResource()->update(
            ['deleted' => 1, 'deleted_in_cycle' => User::getCurrentUser()->getCurrentCycle()->getId()],
            ['id = ?' => $recurringParentId]
        );
    }

    //    public function updateNextRecurring()
    //    {
    //        if (!$this->getRecurring()) {
    //            $this->setDeleted(Application_Model_Entity_System_SystemValues::DELETED_STATUS)->save();
    //            return $this;
    //        }
    //        $nextCycle = $this->getCarrier()->getNotVerifiedSettlementCycle();
    //        if ($nextCycle) {
    //            $startDate = $nextCycle->getCycleStartDate();
    //            $invoiceDate = $this->getNextInvoiceDate($startDate);
    //            if ($this->getInvoiceDate() != $invoiceDate) {
    //                $this->setInvoiceDate($invoiceDate);
    //                $this->save();
    //            }
    //        }
    //    }
}
