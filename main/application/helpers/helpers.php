<?php

if (!function_exists('money_format')) {
    function money_format($number, int $decimals = 2): string
    {
        return number_format((float)$number, $decimals);
    }
}
