<?php

class Application_Model_Recurring_BiWeeklyStrategy extends Application_Model_Recurring_IRecurringStrategy
{
    public function getInvoiceDate($date, $first = false)
    {
        $cycle = $this->entity->getSettlementCycle();
        $dateObject = DateTime::createFromFormat('Y-m-d', $date)->setTime(0, 0);
        $period = $cycle->getCycleMonthPeriod($date);
        $setup = $this->entity->getSetup();
        $biweeklyStartDay = $setup->getBiweeklyStartDay();
        if ($biweeklyStartDay = \Datetime::createFromFormat('Y-m-d', $biweeklyStartDay)) {
            $biweeklyStartDay->setTime(0, 0);
            $dateInterval = $biweeklyStartDay->diff($dateObject);
            if ($dateInterval->invert) {
                $modify = floor($dateInterval->days / 14) * -2;
            } else {
                $modify = ceil($dateInterval->days / 14) * 2;
            }
            $newDate = $biweeklyStartDay->modify($modify . ' week');

            foreach ($period as $dt) {
                if ($dt->format('Y-m-d') == $newDate->format('Y-m-d')) {
                    return $newDate->format('Y-m-d');
                }
            }
        }

        return false;

        $dayOfWeek = $setup->getFirstStartDay();
        $weekOffset = $setup->getWeekOffset();
        $date = null;

        foreach ($period as $dt) {
            if ($dt->format('w') == $dayOfWeek) {
                if ($first) {
                    if ($weekOffset == 0 || $date) {
                        return $dt->format('Y-m-d');
                    } else {
                        $date = $dt->format('Y-m-d');
                    }
                } else {
                    return $dt->format('Y-m-d');
                }
            }
        }

        return false;
    }
}
