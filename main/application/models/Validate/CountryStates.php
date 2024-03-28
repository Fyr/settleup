<?php

class Application_Model_Validate_CountryStates extends Zend_Validate_Abstract
{
    final public const INVALID_STATE = 'invalidState';
    protected $_messageTemplates = [
        self::INVALID_STATE => 'State is required and can\'t be empty',
    ];

    public function isValid($value, $context = null)
    {
        $valid = true;
        $this->_setValue($value);
        if ('-' === $value) {
            $valid = false;
            $this->_error(self::INVALID_STATE);
        }

        return $valid;
    }
}
