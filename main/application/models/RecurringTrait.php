<?php

trait Application_Model_RecurringTrait
{
    public function changeRecurringData($fromDb = false)
    {
        if (in_array($this->getRecurringType(), [
            Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID,
            Application_Model_Entity_System_CyclePeriod::SEMI_WEEKLY_PERIOD_ID,
            Application_Model_Entity_System_CyclePeriod::BIWEEKLY_PERIOD_ID,
        ])) {
            if ($fromDb) {
                $this->setWeekDay($this->getFirstStartDay());
                $this->setSecondWeekDay($this->getSecondStartDay());
            } else {
                $this->setFirstStartDay($this->getWeekDay());
                $this->setSecondStartDay($this->getSecondWeekDay());
            }
        }

        return $this;
    }

    abstract public function getRecurringType();
}
