<?php

class Application_Form_Reporting_ContractorTransactionHistory extends Application_Form_Base
{
    public function init()
    {
        $this->setName('contractor_transaction_history');
        parent::init();

        $contractorId = new Zend_Form_Element_Hidden('contractor_id');

        $contractorIdTitle = new Zend_Form_Element_Text('contractor_id_title');
        $contractorIdTitle->setLabel('Select contractor:')->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        );

        $this->addElements([$contractorId, $contractorIdTitle]);

        $this->setDefaultDecorators(['contractor_id_title']);

        $this->addSubmit('Save');
    }
}
