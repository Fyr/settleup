<?php

require_once 'Zend/Validate/Abstract.php';

class My_Validate_InFuture extends Zend_Validate_Abstract
{

    const NOT_LATER = 'notLaterThan';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_LATER => "'%value%' is earlier than now",
    );

    /**
     *
     * Returns true if and if $value is date later than now
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $date = new Zend_Date($value, 'MM-dd-yyyy');
        $now = new Zend_Date(date('m-d-Y'), 'MM-dd-yyyy');
        $this->_setValue($date->toString('MM-dd-yyyy'));

        if ($date->isEarlier($now)) {
            $this->_error(self::NOT_LATER);
            return false;
        }
        return true;
    }

}
