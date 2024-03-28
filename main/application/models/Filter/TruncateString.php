<?php

class Application_Model_Filter_TruncateString implements Zend_Filter_Interface
{
    public function filter($value)
    {
        $valueFiltered = substr((string) $value, 0, 255);

        return $valueFiltered;
    }
}
