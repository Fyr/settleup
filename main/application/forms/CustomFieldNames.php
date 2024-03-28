<?php

class Application_Form_CustomFieldNames extends Application_Form_Base
{
    public function init()
    {
        $this->setName('custom_field_names');
        parent::init();

        $id = new Application_Form_Element_Hidden('id');

        $carrierId = new Application_Form_Element_Hidden('carrier_id');

        $paymentCode = new Zend_Form_Element_Text('payment_code');
        $paymentCode->setLabel('Compensation Code');

        $carrierPaymentCode = new Zend_Form_Element_Text('carrier_payment_code');
        $carrierPaymentCode->setLabel('Division Compensation Code');

        $description = new Zend_Form_Element_Text('description');
        $description->setLabel('Description');

        $category = new Zend_Form_Element_Text('category');
        $category->setLabel('Category');

        $department = new Zend_Form_Element_Text('department');
        $department->setLabel('Department');

        $glCode = new Zend_Form_Element_Text('gl_code');
        $glCode->setLabel('GL Code');

        $invoice = new Zend_Form_Element_Text('invoice');
        $invoice->setLabel('Invoice');

        $invoiceDate = new Zend_Form_Element_Text('invoice_date');
        $invoiceDate->setLabel('Invoice Date');

        $disbursementCode = new Zend_Form_Element_Text('disbursement_code');
        $disbursementCode->setLabel('Disbursement Code');

        $this->addElements([
            $id,
            $carrierId,
            $paymentCode,
            $carrierPaymentCode,
            $description,
            $category,
            $department,
            $glCode,
            $invoice,
            $invoiceDate,
            $disbursementCode,
        ]);

        $this->setDefaultDecorators([
            'payment_code',
            'carrier_payment_code',
            'description',
            'category',
            'department',
            'gl_code',
            'invoice',
            'invoice_date',
            'disbursement_code',
        ]);
    }
}
