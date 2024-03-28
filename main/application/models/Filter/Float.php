<?php

class Application_Model_Filter_Float implements Zend_Filter_Interface
{
    public function filter($value)
    {
        $valueFiltered = preg_replace('/[^0-9\.\-]/', '', (string) $value);

        return $valueFiltered;
    }
}
