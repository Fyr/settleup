<?php

class Application_Model_System_Daysofweek
{
    final public const DAY_OF_WEEK_1 = 'Sunday';
    final public const DAY_OF_WEEK_2 = 'Monday';
    final public const DAY_OF_WEEK_3 = 'Tuesday';
    final public const DAY_OF_WEEK_4 = 'Wednesday';
    final public const DAY_OF_WEEK_5 = 'Thursday';
    final public const DAY_OF_WEEK_6 = 'Friday';
    final public const DAY_OF_WEEK_7 = 'Saturday';

    public static function getList()
    {
        return [
            1 => self::DAY_OF_WEEK_1,
            2 => self::DAY_OF_WEEK_2,
            3 => self::DAY_OF_WEEK_3,
            4 => self::DAY_OF_WEEK_4,
            5 => self::DAY_OF_WEEK_5,
            6 => self::DAY_OF_WEEK_6,
            7 => self::DAY_OF_WEEK_7,
        ];
    }

    public static function getDayByNumber($num)
    {
        $days = self::getList();

        return $days[$num];
    }
}
