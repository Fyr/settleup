<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Carrier as Carrier;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_Settlement_Rule as Rule;
use Application_Model_Entity_System_CyclePeriod as CyclePeriod;

class Application_Form_Settlement_Rule extends Application_Form_Base
{
    use Application_Form_WeekOptionsTrait;

    public function init()
    {
        $this->setName('settlement_cycle_rule');
        parent::init();

        $id = new Zend_Form_Element_Hidden('id');

        $settlementStartDate = new Zend_Form_Element_Text('cycle_start_date');
        $settlementStartDate->setLabel('Start Date')->setRequired(true)->addValidator('DateDatetime')->setAttrib(
            'class',
            'date'
        );

        $cyclePeriod = new CyclePeriod();

        $settlementCycle = new Zend_Form_Element_Select('cycle_period_id');
        $settlementCycle->setLabel('Settlement Cycle ')->setMultiOptions(
            $cyclePeriod->getResource()->getOptions('title', 'id != ' . CyclePeriod::MONTHLY_SEMI_MONTHLY_ID)
        )->setValue(CyclePeriod::MONTHLY_PERIOD_ID);

        $weekDay = new Zend_Form_Element_Select('week_day');
        $weekDay->setMultiOptions($this->getWeekOptions())->setLabel('Select Days * ');

        $secondWeekDay = new Zend_Form_Element_Select('second_week_day');
        $secondWeekDay->setMultiOptions($this->getWeekOptions())->setLabel('and');

        $firstStartDay = new Zend_Form_Element_Select('first_start_day');
        $firstStartDay->setLabel('Select Days * ')->setRequired(true)->addValidator(
            'Int',
            true,
            ['messages' => 'Entered value is invalid']
        )->addValidator(
            'between',
            false,
            ['min' => '1', 'max' => '31', 'messages' => 'Entered value is invalid']
        )->setMultiOptions(
            $this->getDaysOptions()
        );

        $secondStartDay = new Zend_Form_Element_Select('second_start_day');
        $secondStartDay->setLabel('and')->setRequired(true)->addValidator(
            'Int',
            true,
            ['messages' => 'Entered value is invalid']
        )->addValidator(
            'between',
            false,
            ['min' => '1', 'max' => '31', 'messages' => 'Entered value is invalid']
        )->setMultiOptions(
            $this->getDaysOptions()
        );

        $paymentTerms = new Zend_Form_Element_Text('payment_terms');
        $paymentTerms->setLabel('Processing Deadline (days from period close)')->setRequired(true)->addValidator(
            'Int',
            true,
            ['messages' => 'Entered value is invalid']
        )->addValidator(
            'Between',
            false,
            ['min' => '0', 'max' => 365, 'inclusive' => true, 'messages' => 'Entered value is invalid']
        );

        $disbursementTerms = new Zend_Form_Element_Text('disbursement_terms');
        $disbursementTerms->setLabel('Disbursement Terms (days from period close)')->setRequired(true)->addValidator(
            'Int',
            true,
            ['messages' => 'Entered value is invalid']
        )->addValidator(
            'Between',
            false,
            ['min' => '0', 'max' => 365, 'inclusive' => true, 'messages' => 'Entered value is invalid']
        );

        $changeCycleRuleFields = new Zend_Form_Element_Hidden('change_cycle_rule_fields');

        $lastClosedCycle = new Zend_Form_Element_Text('last_closed_cycle');
        $lastClosedCycle->setLabel('Last Closed Settlement')->setAttrib('disabled', 'disabled');

        $this->addElements(
            [
                $id,
                $settlementCycle,
                $settlementStartDate,
                $changeCycleRuleFields,
                $lastClosedCycle,
                $firstStartDay,
                $secondStartDay,
                $paymentTerms,
                $disbursementTerms,
                $weekDay,
                $secondWeekDay,
            ]
        );
        $this->setDefaultDecorators(
            [
                'cycle_start_date',
                'cycle_period_id',
                'last_closed_cycle',
                'first_start_day',
                'second_start_day',
                'payment_terms',
                'disbursement_terms',
                'week_day',
                'second_week_day',
            ]
        );

        if (!User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_RULE_MANAGE)) {
            foreach ($this->getElements() as $element) {
                $element->setAttrib('readonly', 'readonly');
            };
        } else {
            $this->addSubmit('Save');
        }
    }

    public function populate(array $post)
    {
        parent::populate($post);
        if ($ruleId = $this->getElement('id')->getValue()) {
            $rule = Rule::staticLoad($ruleId);
            if ($carrierId = $rule->getCarrierId()) {
                $carrier = Carrier::staticLoad($carrierId, 'entity_id');
                if ($carrier->getId()) {
                    $lastCycle = $carrier->getLastClosedSettlementCycle();
                    $this->getElement('last_closed_cycle')->setValue($lastCycle->getCyclePeriodString());
                }
            }
        }

        return $this;
    }

    public function configure()
    {
        parent::configure();

        foreach (['first_start_day', 'second_start_day', 'week_day', 'second_week_day'] as $element) {
            $this->getElement($element)->getDecorator('Label')->removeOption('requiredSuffix');
        }

        return $this;
    }
}
