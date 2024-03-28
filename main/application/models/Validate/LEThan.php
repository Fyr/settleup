<?php

class Application_Model_Validate_LEThan extends Zend_Validate_LessThan
{
    public function isValid($value)
    {
        $this->_setValue($value);
        if ($this->_max < $value) {
            $this->_error(self::NOT_LESS);

            return false;
        }

        return true;
    }
}
