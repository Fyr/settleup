<?php

/**
 * @method $this staticLoad($id, $field = null)
 */
class Application_Model_Entity_CustomFieldNames extends Application_Model_Base_Entity
{
    public $reporting = false;

    public function getPaymentCode()
    {
        return $this->getData('payment_code') ?: 'Compensation Code';
    }

    public function getCarrierPaymentCode()
    {
        return $this->getData('carrier_payment_code') ?: 'Division Compensation Code';
    }

    public function getDescription()
    {
        return $this->getData('description') ?: 'Description';
    }

    public function getCategory()
    {
        return $this->getData('category') ?: 'Category';
    }

    public function getDepartment()
    {
        return $this->getData('department') ?: 'Department';
    }

    public function getGlCode()
    {
        return $this->getData('gl_code') ?: 'GL Code';
    }

    public function getInvoice()
    {
        return $this->getData('invoice') ?: 'Invoice';
    }

    public function getInvoiceDate()
    {
        if ($this->getData('invoice_date')) {
            $data = $this->getData('invoice_date');
        } else {
            if ($this->reporting) {
                $data = 'Inv Date';
            } else {
                $data = 'Invoice Date';
            }
        }

        return $data;
    }

    public function getDisbursementCode()
    {
        return $this->getData('disbursement_code') ?: 'Disbursement Code';
    }
}
