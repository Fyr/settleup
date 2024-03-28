<?php

class Application_Form_Reporting_OnDemand_ContractorPaymentHistory extends Application_Form_Base
{
    public function init()
    {
        $this->setName('contractor_payment_history');
        parent::init();

        $contractorId = new Zend_Form_Element_Hidden('contractor_id');

        $contractorIdTitle = new Zend_Form_Element_Text('contractor_id_title');
        $contractorIdTitle->setLabel('Select contractor')->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setAttrib('href', '#contractor_id_modal')->setAttrib('data-toggle', 'modal');

        $firstDate = new Zend_Form_Element_Text('first_date');
        $firstDate->setLabel('From date')->setRequired(true)->addFilter('StripTags')->addFilter('StringTrim');

        $secondDate = new Zend_Form_Element_Text('second_date');
        $secondDate->setLabel('To date')->setRequired(true)->addFilter('StripTags')->addFilter('StringTrim');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Process');

        $this->addElements(
            [
                $contractorId,
                $contractorIdTitle,
                $firstDate,
                $secondDate,
                $submit,
            ]
        );

        $this->setDefaultDecorators(
            [
                'contractor_id',
                'contractor_id_title',
                'first_date',
                'second_date',
                'submit',
            ]
        );
    }
}
