<?php

class Application_Form_Reporting_OnDemand_raVendorTransactionHistory extends Application_Form_Base
{
    public function init()
    {
        $this->setName('ra_vendor_transaction_history');
        parent::init();

        $raVendorId = new Zend_Form_Element_Hidden('ra_vendor_id');

        $raVendorIdTitle = new Zend_Form_Element_Text('ra_vendor_id_title');
        $raVendorIdTitle->setLabel('Select reserve account vendor')->setRequired(true)->addFilter(
            'StripTags'
        )->addFilter('StringTrim')->setAttrib('href', '#ra_vendor_id_modal')->setAttrib('data-toggle', 'modal');

        $firstDate = new Zend_Form_Element_Text('first_date');
        $firstDate->setLabel('From date')->setRequired(true)->addFilter('StripTags')->addFilter('StringTrim');
        $secondDate = new Zend_Form_Element_Text('second_date');
        $secondDate->setLabel('To date')->setRequired(true)->addFilter('StripTags')->addFilter('StringTrim');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Process');

        $this->addElements(
            [
                $raVendorId,
                $raVendorIdTitle,
                $firstDate,
                $secondDate,
                $submit,
            ]
        );

        $this->setDefaultDecorators(
            [
                'ra_vendor_id',
                'ra_vendor_id_title',
                'first_date',
                'second_date',
                'submit',
            ]
        );
    }
}
