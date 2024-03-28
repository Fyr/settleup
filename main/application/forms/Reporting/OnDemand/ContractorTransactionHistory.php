<?php

class Application_Form_Reporting_OnDemand_ContractorTransactionHistory extends Application_Form_Base
{
    public function init()
    {
        $this->setName('contractor_transaction_history');
        parent::init();

        $contractorId = new Zend_Form_Element_Hidden('contractor_id');

        $contractorIdTitle = new Zend_Form_Element_Text('contractor_id_title');
        $contractorIdTitle->setLabel('Select contractor')->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setAttrib('href', '#contractor_id_modal')->setAttrib('data-toggle', 'modal');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Process');

        $this->addElements([$contractorId, $contractorIdTitle, $submit]);

        $this->setDefaultDecorators(
            ['contractor_id', 'contractor_id_title', 'submit']
        );
    }
}
