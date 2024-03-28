<?php

class Application_Form_Reporting_OnDemand_VendorDeductionHistory extends Application_Form_Base
{
    public function init()
    {
        $this->setName('vendor_deduction_history');
        parent::init();

        $vendorId = new Zend_Form_Element_Hidden('vendor_id');

        $vendorIdTitle = new Zend_Form_Element_Text('vendor_id_title');
        $vendorIdTitle->setLabel('Select vendor')->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setAttrib('href', '#vendor_id_modal')->setAttrib('data-toggle', 'modal');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Process');

        $this->addElements([$vendorId, $vendorIdTitle, $submit]);

        $this->setDefaultDecorators(
            ['vendor_id', 'vendor_id_title', 'submit']
        );
    }
}
