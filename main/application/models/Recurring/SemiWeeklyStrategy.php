<?php

class Application_Model_Recurring_SemiWeeklyStrategy extends Application_Model_Recurring_IRecurringStrategy
{
    public function getInvoiceDate($date)
    {
        $cycle = $this->entity->getSettlementCycle();
        $period = $cycle->getCycleMonthPeriod($date);
        $setup = $this->entity->getSetup();
        $firstDay = $setup->getFirstStartDay();
        $secondDay = $setup->getSecondStartDay();
        foreach ($period as $dt) {
            if ($dt->format('w') == $firstDay || $dt->format('w') == $secondDay) {
                return $dt->format('Y-m-d');
            }
        }

        return false;
    }
}
