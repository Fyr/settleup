<?php

class Application_Views_Helpers_Date extends Zend_View_Helper_Abstract
{
    /**
     * @param $date
     * @return mixed
     */
    public function date($date)
    {
        $date = new DateTime($date);

        return $date->format('n/j/Y');
    }
}
