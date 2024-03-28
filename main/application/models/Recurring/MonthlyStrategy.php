<?php

class Application_Model_Recurring_MonthlyStrategy extends Application_Model_Recurring_IRecurringStrategy
{
    public function getInvoiceDate($date)
    {
        $cycle = $this->entity->getSettlementCycle();
        $period = $cycle->getCycleMonthPeriod($date);
        $dayOfMonth = $this->entity->getSetup()->getFirstStartDay();
        if ($dayOfMonth == 0) {
            throw new Exception('Monthly');
        }
        $dataObject = new DateTime($date);
        if ($dataObject->format('t') < $dayOfMonth) {
            return $dataObject->modify('last day of')->format('Y-m-d');
        }
        foreach ($period as $dt) {
            if ($dt->format('d') == $dayOfMonth) {
                return $dt->format('Y-m-d');
            }
        }

        return false;
    }
}
