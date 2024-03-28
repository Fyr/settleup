<?php

class Application_Model_Recurring_SemiMonthlyStrategy extends Application_Model_Recurring_IRecurringStrategy
{
    public function getInvoiceDate($date)
    {
        $cycle = $this->entity->getSettlementCycle();
        $period = $cycle->getCycleMonthPeriod($date);
        $setup = $this->entity->getSetup();
        $firstDay = $setup->getFirstStartDay();
        $secondDay = $setup->getSecondStartDay();
        foreach ($period as $dt) {
            $lastDayOfMonth = $dt->format('t');
            if ($dt->format('d') == $firstDay || $dt->format('d') == $secondDay) {
                return $dt->format('Y-m-d');
            } elseif ($dt->format('d') == $lastDayOfMonth) {
                if ($firstDay > $dt->format('d') || $secondDay > $dt->format('d')) {
                    return $dt->format('Y-m-d');
                }
            }
        }

        return false;
    }
}
