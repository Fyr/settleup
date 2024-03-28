<?php

class Application_Model_Filter_DeleteCommas implements Zend_Filter_Interface
{
    public function filter($value)
    {
        $valueFiltered = str_replace(',', '', (string) $value);

        return $valueFiltered;
    }
}
