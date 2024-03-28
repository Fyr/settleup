<?php

class Application_Form_Settlement_Cycle extends Application_Form_Base
{
    use Application_Form_WeekOptionsTrait;

    public function init()
    {
        $this->setName('settlement_cycle');
        parent::init();

        $id = new Zend_Form_Element_Hidden('id');

        $carrierId = new Zend_Form_Element_Hidden('carrier_id');

        $settlementStartDate = new Zend_Form_Element_Text('cycle_start_date');
        $settlementStartDate->setLabel('Period Start Date')->addValidator('DateDatetime')->setRequired(true);

        $settlementCloseDate = new Zend_Form_Element_Text('cycle_close_date');
        $settlementCloseDate->setLabel('Period Close Date')->addValidator('DateDatetime')->setRequired(true);
        if (!Application_Model_Entity_Accounts_User::getCurrentUser()->isAdmin()) {
            $settlementCloseDate->setAttrib('readonly', 'readonly');
        }

        $processingDate = new Zend_Form_Element_Text('processing_date');
        $processingDate->setLabel('Processing Date')->addValidator('DateDatetime')->setRequired(true);

        $disbursementDate = new Zend_Form_Element_Text('disbursement_date');
        $disbursementDate->setLabel('Disbursement Date')->addValidator('DateDatetime')->setRequired(true);

        $cyclePeriod = new Application_Model_Entity_System_CyclePeriod();

        $settlementCycle = new Zend_Form_Element_Select('cycle_period_id');
        $settlementCycle->setLabel('Settlement Cycle ')->setMultiOptions(
            $cyclePeriod->getResource()->getOptions()
        );

        $weekDay = new Zend_Form_Element_Select('week_day');
        $weekDay->setMultiOptions($this->getWeekOptions())
            //            ->setLabel('Select Days of Week ');
            ->setLabel('Select Days * ');

        $secondWeekDay = new Zend_Form_Element_Select('second_week_day');
        $secondWeekDay->setMultiOptions($this->getWeekOptions())->setLabel('and');

        $firstStartDay = new Zend_Form_Element_Select('first_start_day');
        //        $firstStartDay->setLabel('Select Days of Month ')
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
        $paymentTerms->setLabel('Processing Deadline')->setRequired(true)->addValidator(
            'Int',
            true,
            ['messages' => 'Entered value is invalid']
        );

        $disbursementTerms = new Zend_Form_Element_Text('disbursement_terms');
        $disbursementTerms->setLabel('Disbursement terms')->setRequired(true)->addValidator(
            'Int',
            true,
            ['messages' => 'Entered value is invalid']
        );

        $cycleStatus = new Zend_Form_Element_Text('cycle_status');
        $cycleStatus->setLabel('Cycle Status')->setAttrib('readonly', 'readonly');

        $this->addElements(
            [
                $id,
                $carrierId,
                $settlementCycle,
                $settlementStartDate,
                $settlementCloseDate,
                $processingDate,
                $disbursementDate,
                $firstStartDay,
                $secondStartDay,
                $cycleStatus,
                $weekDay,
                $secondWeekDay,
                //                $paymentTerms,
                //                $disbursementTerms
            ]
        );
        $this->setDefaultDecorators(
            [
                'cycle_start_date',
                'cycle_close_date',
                'cycle_period_id',
                'settlement_day',
                'cycle_status',
                'first_start_day',
                'second_start_day',
                'payment_terms',
                'processing_date',
                'disbursement_date',
                'disbursement_terms',
                'week_day',
                'second_week_day',
            ]
        );
        $this->addSubmit('Save');
    }

    public function setupForEditAction()
    {
        if ($this->cycle_period_id->getValue() != Application_Model_Entity_System_CyclePeriod::SEMY_MONTHLY_PERIOD_ID) {
            $this->first_start_day->setAttrib('readonly', 'readonly');
            $this->second_start_day->setAttrib('readonly', 'readonly');
            if (!Application_Model_Entity_Accounts_User::getCurrentUser()->isAdmin()) {
                $this->cycle_start_date->setAttrib('readonly', 'readonly');
            }
        }

        $this->cycle_period_id->setAttrib('readonly', 'readonly');
    }

    public function setupForNewAction()
    {
        $this->cycle_start_date->setAttrib('class', 'date');
    }

    public function configure()
    {
        parent::configure();
        foreach (['first_start_day', 'second_start_day', 'week_day', 'second_week_day'] as $element) {
            $this->getElement($element)->getDecorator('Label')->removeOption('requiredSuffix');
        }

        return $this;
    }

    public function isValid($data)
    {
        if ($data['id']) {
            $this->cycle_period_id->setAttrib('readonly', 'readonly');
            $settlementCycle = new Application_Model_Entity_Settlement_Cycle();
            $settlementCycle->load($data['id']);
            $data['cycle_period_id'] = $settlementCycle->getCyclePeriodId();
            if ($settlementCycle->getCyclePeriodId(
            ) != Application_Model_Entity_System_CyclePeriod::SEMY_MONTHLY_PERIOD_ID) {
                $this->first_start_day->setRequired(false);
                $this->second_start_day->setRequired(false);
            }
        }

        return parent::isValid($data);
    }

    public function configureForm($post)
    {
        $periodId = $post['cycle_period_id'];
        switch ($periodId) {
            case Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID:
            case Application_Model_Entity_System_CyclePeriod::BIWEEKLY_PERIOD_ID:
            case Application_Model_Entity_System_CyclePeriod::SEMI_WEEKLY_PERIOD_ID:
                $this->first_start_day->setRequired(false);
                $this->second_start_day->setRequired(false);
                unset($post['first_start_day']);
                unset($post['second_start_day']);
                break;
            case Application_Model_Entity_System_CyclePeriod::MONTHLY_PERIOD_ID:
                $this->second_start_day->setRequired(false);
                $this->week_day->setRequired(false);
                unset($post['second_start_day']);
                unset($post['week_day']);
                unset($post['second_week_day']);
                break;
            case Application_Model_Entity_System_CyclePeriod::MONTHLY_SEMI_MONTHLY_ID:
            case Application_Model_Entity_System_CyclePeriod::SEMY_MONTHLY_PERIOD_ID:
                $this->week_day->setRequired(false);
                unset($post['week_day']);
                unset($post['second_week_day']);
                break;
        }
    }

    public function configureCloseDate($cycleId)
    {
        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load($cycleId);
        if ($ruleDate = $cycle->getRuleDateInNextCycle()) {
            $ruleDateString = $ruleDate->subDay(1)->toString('MM/dd/yyyy');
            $this->removeElement('cycle_close_date');
            $settlementCloseDate = new Zend_Form_Element_Select('cycle_close_date');
            $cycle->changeDateFormat(['cycle_close_date'], true);
            $settlementCloseDate->setLabel('Period Close Date')->setMultiOptions([
                    $cycle->getCycleCloseDate() => $cycle->getCycleCloseDate(),
                    $ruleDateString => $ruleDateString,
                ]);
            $this->addElement($settlementCloseDate);
            $this->setDefaultDecorators(['cycle_close_date']);
        }
    }
}
