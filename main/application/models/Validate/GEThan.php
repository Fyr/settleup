<?php

class Application_Model_Validate_GEThan extends Zend_Validate_GreaterThan
{
    public function isValid($value)
    {
        $this->_setValue($value);
        if ($this->_min > $value) {
            $this->_error(self::NOT_GREATER);

            return false;
        }

        return true;
    }
}
