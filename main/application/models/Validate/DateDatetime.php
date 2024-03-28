<?php

class Application_Model_Validate_DateDatetime extends Zend_Validate_Abstract
{
    final public const INVALID_FORMAT = 'invalidFormat';
    protected $_messageTemplates = [
        self::INVALID_FORMAT => 'Date has invalid format',
    ];
    protected $context;

    public function isValid($date)
    {
        $format = 'm/d/Y';
        $d = DateTime::createFromFormat($format, $date);
        if ($d && $d->format($format) == $date) {
            return true;
        } else {
            $this->_error(self::INVALID_FORMAT);

            return false;
        }
    }
}
