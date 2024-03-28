<?php

trait Application_Form_WeekOptionsTrait
{
    public function getDaysOptions()
    {
        $days = [
            1 => '1st',
            2 => '2nd',
            3 => '3rd',
        ];
        for ($i = 4; $i <= 30; $i++) {
            $days[$i] = $i . 'th';
        }
        $days[21] = '21st';
        $days[22] = '22nd';
        $days[23] = '23rd';
        $days[31] = 'Last';

        return $days;
    }

    public function getWeekOptions()
    {
        return [
            'Sunday',
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
        ];
    }

    public function getFirstWeekOptions()
    {
        $days = $this->getWeekOptions();
        unset($days[6]);
    }

    public function getSecondWeekOptions()
    {
        $days = $this->getWeekOptions();
        unset($days[0]);
    }
}
