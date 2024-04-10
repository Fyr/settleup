<?php

class Application_Form_Deductions_Setup extends Application_Form_Base
{
    use Application_Form_WeekOptionsTrait;

    public function init()
    {
        $this->setName('deductionsetup');
        parent::init();

        $id = new Zend_Form_Element_Hidden('id');

        $providerId = new Zend_Form_Element_Hidden('provider_id');

        $providerIdTitle = new Zend_Form_Element_Text('provider_id_title');
        $providerIdTitle->setLabel('Vendor')->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setAttrib('href', '#provider_id_modal')->setAttrib('data-toggle', 'modal');

        $deductionCode = (new Zend_Form_Element_Text('deduction_code'))
            ->setLabel('Deduction Code')
            ->setRequired()
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $deductionCode->addValidator('DeductionPaymentCode', false, [
            'table' => 'deduction_setup',
            'field' => 'deduction_code',
        ]);

        $description = new Zend_Form_Element_Text('description');
        $description->setLabel('Description')->setRequired(true)->addFilter('StripTags')->addFilter('StringTrim');

        $category = new Zend_Form_Element_Text('category');
        $category->setLabel('Category')->addFilter('StripTags')->addFilter('StringTrim');

        $department = new Zend_Form_Element_Text('department');
        $department->setLabel('Department')->addFilter('StripTags')->addFilter('StringTrim');

        $glCode = new Zend_Form_Element_Text('gl_code');
        $glCode->setLabel('GL Code')->addFilter('StripTags')->addFilter('StringTrim');

        $quantity = new Zend_Form_Element_Text('quantity');
        $quantity->setLabel('Quantity')->addFilter(new Application_Model_Filter_DeleteCommas())->addValidator(
            'Int',
            true,
            ['messages' => 'Entered value is invalid']
        )->addValidator(
            'Between',
            true,
            [
                'min' => -1_000_000,
                'max' => 1_000_000,
                'messages' => 'Entered value should be between -1,000,000 and 1,000,000',
            ]
        )->setRequired(true);

        $disbursementCode = new Zend_Form_Element_Text('disbursement_code');
        $disbursementCode->setLabel('Disbursement Code')->addFilter('StripTags')->addFilter('StringTrim');

        $recurring = new Zend_Form_Element_Checkbox('recurring');
        $recurring->setLabel('Recurring');

        $billingCycleModel = new Application_Model_Entity_System_CyclePeriod();
        $billingCycle = new Zend_Form_Element_Select('billing_cycle_id');
        $billingCycle->setLabel('Frequency * ')->addmultiOptions(
            $billingCycleModel->getResource()->getOptions()
        );

        $weekDay = new Zend_Form_Element_Select('week_day');
        $weekDay->setMultiOptions($this->getWeekOptions())
            //            ->setLabel('Select Days of Week ');
            ->setLabel('Select Days * ');

        $secondWeekDay = new Zend_Form_Element_Select('second_week_day');
        $secondWeekDay->setMultiOptions($this->getWeekOptions())->setLabel('and');

        $biweeklyStartDay = new Zend_Form_Element_Text('biweekly_start_day');
        $biweeklyStartDay->setLabel('Start Date * ')
            //                         ->addValidator('Date', false, array('MM/dd/yyyy'))
            ->addValidator('DateDatetime');

        $startDate = new Zend_Form_Element_Text('start_date');
        //        $startDate->setLabel('Start Date *');
        $startDate->setLabel('Select Days * ');
        $startDate->setAttrib('disabled', 'disabled');

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

        $terms = new Zend_Form_Element_Text('terms');
        $terms->setLabel('Terms')->addValidator('Int', true, ['messages' => 'Entered value is invalid']);

        $rate = new Application_Form_Element_Money('rate');
        $rate->setLabel('Rate')->setRequired(true);

        $changeCycleRuleFields = new Application_Form_Element_Hidden('change_cycle_rule_fields');

        $contractorId = new Zend_Form_Element_Hidden('contractor_id');

        $contractorName = new Zend_Form_Element_Text('contractor_name');
        $contractorName->setLabel('Company')->setAttrib('readonly', 'readonly');

        $levelId = new Zend_Form_Element_Hidden('level_id');

        $this->addElements([
            $id,
            $providerId,
            $providerIdTitle,
            $deductionCode,
            $description,
            $category,
            $department,
            $glCode,
            $contractorName,
            $quantity,
            $disbursementCode,
            $terms,
            $recurring,
            $billingCycle,
            $firstStartDay,
            $secondStartDay,
            $rate,
            $levelId,
            $changeCycleRuleFields,
            $weekDay,
            $secondWeekDay,
            $biweeklyStartDay,
            $startDate,
            $contractorId,
        ]);

        $this->setDefaultDecorators([
            'provider_id_title',
            'deduction_code',
            'description',
            'category',
            'department',
            'gl_code',
            'quantity',
            'disbursement_code',
            'first_start_day',
            'second_start_day',
            'recurring',
            'billing_cycle_id',
            'terms',
            'rate',
            'week_day',
            'second_week_day',
            'contractor_name',
            'biweekly_start_day',
            'start_date',
        ]);
        $this->addSubmit('Save');
    }

    public function setupForEditAction()
    {
        parent::setupForEditAction();
        $entity = new Application_Model_Entity_Entity();
        $entity->load($this->provider_id->getValue());
        $this->provider_id_title->setAttrib('disabled', 'disabled');
        $this->provider_id_title->setValue($entity->getName());

        $this->deduction_code->setAttrib('readonly', 'readonly');

        $biweeklyStartDay = $this->getElement('biweekly_start_day')->getValue();
        if ($biweeklyStartDay) {
            $biweeklyStartDay = DateTime::createFromFormat('m/d/Y', $biweeklyStartDay);
            if ($biweeklyStartDay !== false) {
                $this->getElement('start_date')->setValue($biweeklyStartDay->format('l'));
            }
        }

        $deductionSetup = Application_Model_Entity_Deductions_Setup::staticLoad((int)$this->id->getValue());
        if ($deductionSetup->getContractorId()) {
            $this->contractor_name->setValue($deductionSetup->getContractor()->getCompanyName());
        }

        if ($deductionSetup->getLevelId() == Application_Model_Entity_System_SetupLevels::INDIVIDUAL_LEVEL_ID) {
            $this->deduction_code->removeValidator('DeductionPaymentCode');
        }
    }

    public function setupForNewAction()
    {
        if (!$this->provider_id->getValue()) {
            $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();
            if ($userEntity->isOnboarding()) {
                $entity = $userEntity->getEntity();
                $this->provider_id->setValue($entity->getEntityId());
                $this->provider_id_title->setValue($entity->getName())->setAttrib('readonly', 'readonly');
            }
        }
    }

    public function configure()
    {
        parent::configure();
        if (!Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
            Application_Model_Entity_Entity_Permissions::TEMPLATE_MANAGE
        )) {
            $this->readonly();
        }
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
    }

    public function isValid($data)
    {
        if ($data['id']) {
            $this->provider_id_title->setRequired(false);
            $this->deduction_code->removeValidator('DeductionPaymentCode');
        }
        if (isset($data['billing_cycle_id']) && $data['billing_cycle_id'] == Application_Model_Entity_System_CyclePeriod::BIWEEKLY_PERIOD_ID && isset($data['recurring']) && (int)$data['recurring']) {
            $this->biweekly_start_day->setRequired(true);
        }

        return parent::isValid($data);
    }
}
