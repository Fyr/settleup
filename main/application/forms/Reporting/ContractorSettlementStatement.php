<?php

class Application_Form_Reporting_ContractorSettlementStatement extends Application_Form_Base
{
    public function init()
    {
        $this->setName('contractor_settlement_statement');
        parent::init();

        $calculations = new Zend_Form_Element_Select('calculations');
        $calculations->setLabel('Calculations:')->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setMultiOptions(
            [
                    '1' => 'Sum of Compensation.amount',
                '2' => 'Sum of Deduction.amount - Sum of Deduction.adjusted' . ' balance',
                    '3' => 'Sum of Compensation.amount - (Sum of Deduction.amount -' . ' Sum of Deduction.adjusted balance)',
                '4' => 'Sum of Reserve Transaction - Contractor.amount -' . ' Sum of Reserve Transaction Contractor.Adjusted Balance',
                '5' => 'Reserve Account - Contractor.current balance -' . ' (Sum of Reserve Transaction - Contractor.amount - ' . 'Sum of Reserve Transaction Contractor.Adjusted Balance)',
                    '6' => 'Sum of Compensation.amount - (Sum of Deduction.amount -' . ' Sum of Deduction.adjusted balance) - (Sum of Reserve' . ' Transaction - Contractor.amount - Sum of Reserve' . ' Transaction) Contractor.Adjusted Balance',
            ]
        );

        $criteria = new Zend_Form_Element_Select('criteria');
        $criteria->setLabel('Selection Criteria:')->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setMultiOptions(
            [
                '1' => 'For a single carrier. Carrier.name = X',
                    '2' => 'All contractors that have a Compensation.cycle close date' . ', Deduction.cycle close date or Reserve Transaction' . ' - Contractor.settlement cycle date = Current Cycle' . ' close date.',
                    '3' => 'All compensations where Compensation.cycle close date =' . ' current cycle close date',
                '4' => 'All deductions where Deduction.cycle close date =' . ' current cycle close date',
                '5' => 'All reserve transactions where Reserve' . ' Transaction.settlement cycle date = current cycle close' . ' date',
                '6' => 'All reserve accounts - contractors that are' . ' associated with the contractors from selection' . ' criteria 2.',
                '7' => 'All vendors associated with selection criteria 4',
            ]
        );

        $sort = new Zend_Form_Element_Select('sort');
        $sort->setLabel('Sort/Section Criteria:')->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setMultiOptions(
            [
                '1' => 'One report per Contractor',
                '2' => 'Combine all reports for Contractor.delivery ' . '= Mail into single print file. See below.',
            ]
        );

        $delivery = new Zend_Form_Element_Select('delivery');
        $delivery->setLabel('Delivery:')->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setMultiOptions(
            [
                '1' => 'Print - Create one large print file that can be' . ' sent to a local printer or emailed to an outsourced' . ' printing house.',
                '2' => 'Print individual report to PDF and attach to email' . ' or fax and delivered based upon Contractor.delivery' . ' and (Contractor.email 1 or Contractor.fax)',
            ]
        );

        $this->addElements([$calculations, $criteria, $sort, $delivery]);

        $this->setDefaultDecorators(
            ['calculations', 'criteria', 'sort', 'delivery']
        );

        $this->addSubmit('Create');
    }
}
