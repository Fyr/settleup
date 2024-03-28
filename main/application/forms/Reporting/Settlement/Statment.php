<?php

class Application_Form_Reporting_Settlement_Statment extends Application_Form_Base
{
    public function init()
    {
        $this->setName('contractor_payment_history');
        parent::init();

        $cycleId = new Zend_Form_Element_Hidden('cycle_id');

        $cycleIdTitle = new Zend_Form_Element_Text('cycle_id_title');
        $cycleIdTitle->setLabel('Select cycle')->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setAttrib('href', '#cycle_id_modal')->setAttrib('data-toggle', 'modal');

        $contractorId = new Zend_Form_Element_Hidden('contractor_id');

        $contractorIdTitle = new Zend_Form_Element_Text('contractor_id_title');
        $contractorIdTitle->setLabel('Select contractor')->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setAttrib('href', '#contractor_id_modal')->setAttrib('data-toggle', 'modal');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Process');

        $this->addElements(
            [
                $cycleId,
                $cycleIdTitle,
                $contractorId,
                $contractorIdTitle,
                $submit,
            ]
        );

        $this->setDefaultDecorators(
            [
                'cycle_id',
                'cycle_id_title',
                'contractor_id',
                'contractor_id_title',
                'submit',
            ]
        );
    }
}
