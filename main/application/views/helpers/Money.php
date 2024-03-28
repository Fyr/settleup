<?php

class Application_Views_Helpers_Money extends Zend_View_Helper_Abstract
{
    /**
     * @param $value
     * @return string
     */
    public function money($value)
    {
        $negativeSign = '';
        if ($value < 0) {
            $value = $value * -1;
            $negativeSign = '-';
        }

        return sprintf('<span class="num">%s$%s</span>', $negativeSign, number_format($value, 2));
    }
}
