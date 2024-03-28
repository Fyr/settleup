<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Payments_TaxableType as TaxableType;

class Application_Form_Payments_Setup extends Application_Form_Base
{
    use Application_Form_WeekOptionsTrait;

    public function init()
    {
        $fieldNames = User::getCurrentUser()->getSelectedCarrier()->getCustomFieldNames();
        $this->setName('payment_setup');
        parent::init();

        $id = new Zend_Form_Element_Hidden('id');

        $levelId = new Zend_Form_Element_Hidden('level_id');

        $carrierId = new Zend_Form_Element_Hidden('carrier_id');

        $paymentCode = (new Zend_Form_Element_Text('payment_code'))
            ->setLabel($fieldNames->getPaymentCode())
            ->setRequired()
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('DeductionPaymentCode', true, [
                'table' => 'payment_setup',
                'field' => 'payment_code',
            ]);

        $carrierPaymentCode = new Zend_Form_Element_Text('carrier_payment_code');
        $carrierPaymentCode->setLabel($fieldNames->getCarrierPaymentCode())->addFilter('StripTags')->addFilter(
            'StringTrim'
        );

        $description = new Zend_Form_Element_Text('description');
        $description->setLabel($fieldNames->getDescription())->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        );

        $category = new Zend_Form_Element_Text('category');
        $category->setLabel($fieldNames->getCategory())->addFilter('StripTags')->addFilter('StringTrim');

        $terms = new Zend_Form_Element_Text('terms');
        $terms->setLabel('Terms ')->addValidator('Int', true, ['messages' => 'Entered value is invalid']);

        $department = new Zend_Form_Element_Text('department');
        $department->setLabel($fieldNames->getDepartment())->addFilter('StripTags')->addFilter('StringTrim');

        $glCode = new Zend_Form_Element_Text('gl_code');
        $glCode->setLabel($fieldNames->getGlCode())->addFilter('StripTags')->addFilter('StringTrim');

        $quantity = new Zend_Form_Element_Text('quantity');
        $quantity->setLabel('Quantity ')->setRequired(true)->addFilter(
            new Application_Model_Filter_DeleteCommas()
        )->addValidator(
            'Between',
            true,
            [
                'min' => -1_000_000,
                'max' => 1_000_000,
                'messages' => 'Entered value should be between -1,000,000 and 1,000,000',
            ]
        )->addValidator('Int', true, ['messages' => 'Entered value is invalid']);

        $disbursementCode = new Zend_Form_Element_Text('disbursement_code');
        $disbursementCode->setLabel($fieldNames->getDisbursementCode())->addFilter('StripTags')->addFilter(
            'StringTrim'
        );

        $recurring = new Zend_Form_Element_Checkbox('recurring');
        $recurring->setLabel('Recurring ');

        $taxable = (new Zend_Form_Element_Select('taxable'))
            ->setLabel('Taxable')
            ->setMultiOptions((new TaxableType())->getTaxableOptions());

        $cycleEntity = new Application_Model_Entity_System_CyclePeriod();
        $billingCycle = new Zend_Form_Element_Select('billing_cycle_id');
        $billingCycle->setLabel('Frequency * ')->setMultiOptions(
            $cycleEntity->getResource()->getOptions()
        );

        $weekDay = new Zend_Form_Element_Select('week_day');
        $weekDay->setMultiOptions($this->getWeekOptions())
            //            ->setLabel('Select Days of Week ');
            ->setLabel('Select Days * ');

        $biweeklyStartDay = new Zend_Form_Element_Text('biweekly_start_day');
        $biweeklyStartDay->setLabel('Start Date * ')
            //            ->addValidator('Date', false, array('MM/dd/yyyy'))
            ->addValidator('DateDatetime');

        $startDate = new Zend_Form_Element_Text('start_date');
        $startDate->setLabel('Select Days * ');
        $startDate->setAttrib('disabled', 'disabled');

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

        $rate = new Application_Form_Element_Money('rate');
        $rate->setLabel('Rate ')->setRequired(true);

        $changeCycleRuleFields = new Application_Form_Element_Hidden('change_cycle_rule_fields');

        $contractorId = new Application_Form_Element_Hidden('contractor_id');

        $contractorName = new Zend_Form_Element_Text('contractor_name');
        $contractorName->setLabel('Company')->setAttrib('readonly', 'readonly');

        $this->addElements(
            [
                $id,
                $levelId,
                $carrierId,
                $paymentCode,
                $contractorName,
                $carrierPaymentCode,
                $description,
                $category,
                $terms,
                $department,
                $contractorId,
                $glCode,
                $quantity,
                $disbursementCode,
                $recurring,
                $billingCycle,
                $biweeklyStartDay,
                $startDate,
                $firstStartDay,
                $secondStartDay,
                $rate,
                $changeCycleRuleFields,
                $weekDay,
                $secondWeekDay,
                $taxable,
            ]
        );

        $this->setDefaultDecorators(
            [
                'payment_code',
                'carrier_payment_code',
                'contractor_name',
                'description',
                'category',
                'terms',
                'department',
                'gl_code',
                'quantity',
                'disbursement_code',
                'recurring',
                'billing_cycle_id',
                'biweekly_start_day',
                'start_date',
                'first_start_day',
                'second_start_day',
                'rate',
                'week_day',
                'second_week_day',
                'taxable',
            ]
        );

        if (!User::getCurrentUser()->hasPermission(
            Application_Model_Entity_Entity_Permissions::TEMPLATE_MANAGE
        )) {
            foreach ($this->getElements() as $element) {
                $element->setAttrib('readonly', 'readonly');
            }
        } else {
            $this->addSubmit('Save');
        }
    }

    public function setupForEditAction()
    {
        parent::setupForEditAction();
        $this->payment_code->setAttrib('readonly', 'readonly');

        $paymentSetup = Application_Model_Entity_Payments_Setup::staticLoad($this->getId());
        if ($paymentSetup->getContractorId()) {
            $this->contractor_name->setValue($paymentSetup->getContractor()->getCompanyName());
        }

        $biweeklyStartDay = $this->getElement('biweekly_start_day')->getValue();
        if ($biweeklyStartDay) {
            $biweeklyStartDay = DateTime::createFromFormat('m/d/Y', $biweeklyStartDay);
            if ($biweeklyStartDay !== false) {
                $this->getElement('start_date')->setValue($biweeklyStartDay->format('l'));
            }
        }

        if ($paymentSetup->getLevelId() == Application_Model_Entity_System_SetupLevels::INDIVIDUAL_LEVEL_ID) {
            $this->payment_code->removeValidator('DeductionPaymentCode');
        }

        return $this;
    }

    public function isValid($data)
    {
        if ($data['id']) {
            $this->payment_code->removeValidator('DeductionPaymentCode');
        }
        if (isset($data['billing_cycle_id']) && $data['billing_cycle_id'] == Application_Model_Entity_System_CyclePeriod::BIWEEKLY_PERIOD_ID && isset($data['recurring']) && (int)$data['recurring']) {
            $this->biweekly_start_day->setRequired(true);
        }

        return parent::isValid($data);
    }

    public function configure()
    {
        parent::configure();
        foreach (
            [
                'first_start_day',
                'second_start_day',
                'week_day',
                'second_week_day',
                'billing_cycle_id',
                'biweekly_start_day',
                'start_date',
            ] as $element
        ) {
            $this->getElement($element)->getDecorator('Label')->removeOption('requiredSuffix');
        }

        return $this;
    }
}
