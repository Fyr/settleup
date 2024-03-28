<?php

class Application_Model_Validate_DateEEThan extends Zend_Validate_Abstract
{
    final public const EARLY_THAN = 'earlyThan';
    final public const INVALID_FORMAT = 'invalidFormat';
    protected $_messageTemplates = [
        self::EARLY_THAN => 'Invoice Date can\'t be later than Cycle Close Date',
        self::INVALID_FORMAT => 'Invoice Date has invalid format (acceptable: mm/dd/yyyy)',
    ];
    protected $context;

    public function isValid($value, $context = null)
    {
        if (isset($context['priority'])) {
            $entity = new Application_Model_Entity_Deductions_Deduction();
        } else {
            $entity = new Application_Model_Entity_Payments_Payment();
        }
        $entity->load($context['id']);
        $closeDate = $entity->getSettlementCycle()->getCycleCloseDate();

        if (Zend_Date::isDate($value, 'm/d/Y')) {
            $closeDate = DateTime::createFromFormat('Y-m-d', $closeDate)->setTime(0, 0, 0);
            if (!$invoiceDate = DateTime::createFromFormat('m/d/Y', $value)) {
                $this->_error(self::INVALID_FORMAT);

                return false;
            }
            $invoiceDate->setTime(0, 0, 0);

            if ($invoiceDate > $closeDate) {
                $this->_error(self::EARLY_THAN);

                return false;
            }

            return true;
        } else {
            $this->_error(self::INVALID_FORMAT);

            return false;
        }
    }
}
