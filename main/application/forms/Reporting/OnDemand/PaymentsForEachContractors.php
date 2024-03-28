<?php

class Application_Form_Reporting_OnDemand_PaymentsForEachContractors extends Application_Form_Base
{
    public function init()
    {
        $this->setName('payments_for_each_contractors');
        parent::init();

        $year = new Zend_Form_Element_Select('year');
        $year->setLabel('Type a settlement year:')->setRequired(true)->setMultiOptions($this->getRange());
        ;
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Process');

        $this->addElements([$year, $submit]);

        $this->setDefaultDecorators(['year', 'submit']);
    }

    public function getRange()
    {
        $range = [];
        $currentYear = new Zend_Date();
        $currentYear = (int)$currentYear->toString(Zend_Date::YEAR);
        for ($year = 2000; $year <= $currentYear; $year++) {
            $range[$year] = $year;
        }

        return $range;
    }
}
