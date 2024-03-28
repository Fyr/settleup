<?php

class Application_Model_Entity_System_CyclePeriod extends Application_Model_Base_Entity
{
    final public const WEEKLY_PERIOD_ID = 1;
    final public const BIWEEKLY_PERIOD_ID = 2;
    final public const MONTHLY_PERIOD_ID = 3;
    final public const SEMY_MONTHLY_PERIOD_ID = 4;
    final public const MONTHLY_SEMI_MONTHLY_ID = 5;
    final public const SEMI_WEEKLY_PERIOD_ID = 6;
    final public const DATE_OR_DATETIME_PATTERN = '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])( [0-9]{2}:[0-9]{2}:[0-9]{2})?$/';

    /**
     * Returns cycle close date
     *
     * @return Zend_Date
     * @throws Exception 'Undefined cycle period'
     */
    public function getPeriodLength(Application_Model_Entity_Settlement_Cycle $cycleEntity)
    {
        $startDate = $cycleEntity->getCycleStartDate();
        $startDate = new Zend_Date($startDate, Zend_Date::ISO_8601);

        switch ($this->getId()) {
            case self::WEEKLY_PERIOD_ID:
                $closeDate = clone $startDate;
                $closeDate->addWeek(1);
                break;
            case self::BIWEEKLY_PERIOD_ID:
                $closeDate = clone $startDate;
                $closeDate->addWeek(2);
                break;
            case self::SEMY_MONTHLY_PERIOD_ID:
                $endDate = clone $startDate;
                $closeDate = clone $startDate;
                $endDate->addMonth(1);
                for ($date = clone $startDate; $endDate->compare($date) > 0; $date->addDay(1)) {
                    $lastDayOfMonth = clone $date;
                    $lastDayOfMonth = $lastDayOfMonth->setDay(1)->addMonth(1)->subDay(1)->get(Zend_Date::DAY);
                    $monthDay = $date->get(Zend_Date::DAY);
                    if (($monthDay == $cycleEntity->getFirstStartDay() || $monthDay == $cycleEntity->getSecondStartDay(
                    )) && $startDate->compare($date) < 0) {
                        $closeDate = $date;
                        break;
                    } elseif ($lastDayOfMonth == $date->get(Zend_Date::DAY)) {
                        if (($cycleEntity->getFirstStartDay() > $lastDayOfMonth || $cycleEntity->getSecondStartDay(
                        ) > $lastDayOfMonth) && $startDate->compare($date) < 0) {
                            $closeDate = $date;
                            break;
                        }
                    }
                }
                break;
            case self::MONTHLY_PERIOD_ID:
                $closeDate = clone $startDate;
                $closeDate->addMonth(1);
                break;

            case self::SEMI_WEEKLY_PERIOD_ID:
                $endDate = clone $startDate;
                $endDate->addWeek(1);
                $closeDate = clone $startDate;
                for ($date = clone $startDate; $endDate->compare($date) > 0; $date->addDay(1)) {
                    $weekDay = $date->get(Zend_Date::WEEKDAY_DIGIT);
                    if (($weekDay == $cycleEntity->getFirstStartDay() || $weekDay == $cycleEntity->getSecondStartDay(
                    )) && $startDate->compare($date) < 0) {
                        $closeDate = $date;
                        break;
                    }
                }
                break;
            default:
                throw new Exception('Undefined cycle period');
        }

        if ($startDate->compare($closeDate) !== 0) {
            $closeDate->subDay(1);
        }

        return $closeDate;
    }

    public function getBillingCycles($billingId)
    {
        $billingCycles = $this->getResource()->getOptions();
        $billingCycleId = null;
        if ($billingId == self::MONTHLY_SEMI_MONTHLY_ID) {
            $billingCycleId = self::SEMY_MONTHLY_PERIOD_ID;
        } else {
            $billingCycleId = self::MONTHLY_SEMI_MONTHLY_ID;
        }
        unset($billingCycles[$billingCycleId]);

        return $billingCycles;
    }
}
