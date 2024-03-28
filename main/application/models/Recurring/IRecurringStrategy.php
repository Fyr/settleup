<?php

abstract class Application_Model_Recurring_IRecurringStrategy
{
    /**
     * @param Application_Model_Entity_Deductions_Deduction|Application_Model_Entity_Payments_Payment $entity
     */
    public function __construct(protected $entity)
    {
    }

    abstract public function getInvoiceDate($date);
}
