<?php

require_once 'Zend/Validate/Abstract.php';

class My_Validate_GreaterThanZero extends Zend_Validate_Abstract
{

    const NOT_GREATER = 'notGreaterThan';
    const ZERO = 0;

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_GREATER => "'%value%' is not equal or greater than zero",
    );

    /**
     *
     * Returns true if and if $value is equal or greater than zero
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue($value);

        if (self::ZERO > $value) {
            $this->_error(self::NOT_GREATER);
            return false;
        }
        return true;
    }

}
