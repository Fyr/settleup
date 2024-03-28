<?php

class Application_Model_Recurring_WeeklyStrategy extends Application_Model_Recurring_IRecurringStrategy
{
    public function getInvoiceDate($date)
    {
        $cycle = $this->entity->getSettlementCycle();
        $period = $cycle->getCycleMonthPeriod($date);
        $dayOfWeek = $this->entity->getSetup()->getFirstStartDay();
        foreach ($period as $dt) {
            if ($dt->format('w') == $dayOfWeek) {
                return $dt->format('Y-m-d');
            }
        }

        return false;
    }
}
