<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_Settlement_Cycle as Cycle;
use Application_Model_Entity_System_CyclePeriod as CyclePeriod;
use Application_Model_Entity_System_SettlementCycleStatus as CycleStatus;

class Application_Form_Deductions_Deduction extends Application_Form_Base
{
    use Application_Form_WeekOptionsTrait;

    public function init()
    {
        $this->setName('deductions');
        parent::init();

        $providerId = new Zend_Form_Element_Hidden('provider_id');

        $setupId = new Zend_Form_Element_Hidden('setup_id');
        $setupId->setRequired(true);

        $id = new Zend_Form_Element_Text('id');
        $id->setLabel('ID')->addFilter('StripTags')->addFilter('StringTrim')->setAttrib(
            'readonly',
            'readonly'
        );

        $contractorName = new Zend_Form_Element_Text('contractor_name');
        $contractorName->setLabel('Contractor ')->addFilter('StripTags')->addFilter('StringTrim')->setAttrib(
            'readonly',
            'readonly'
        );

        $contractorCode = new Zend_Form_Element_Text('contractor_code');
        $contractorCode->setLabel('Contractor Code ')->addFilter('StripTags')->addFilter('StringTrim')->setAttrib(
            'readonly',
            'readonly'
        );

        $deductionCode = new Zend_Form_Element_Text('deduction_code');
        $deductionCode->setLabel('Code')->addFilter('StripTags')->addFilter('StringTrim')->setAttrib(
            'readonly',
            'readonly'
        );

        $description = new Zend_Form_Element_Text('description');
        $description->setLabel('Description ')->addFilter('StripTags')->addFilter('StringTrim');

        $invoiceDate = new Zend_Form_Element_Text('invoice_date');
        $invoiceDate->setLabel('Transaction Date ')->addValidator('DateDatetime')->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setRequired(true);

        $invoiceDueDate = new Application_Form_Element_Hidden('invoice_due_date');

        $department = new Zend_Form_Element_Text('department');
        $department->setLabel('Department ')->addFilter('StripTags')->addFilter('StringTrim');

        $powerunitCode = new Zend_Form_Element_Text('powerunit_code');
        $powerunitCode->setLabel('Power Unit ')->addFilter('StripTags')->addFilter('StringTrim')
            ->setAttrib('readonly', 'readonly');

        $reference = new Zend_Form_Element_Text('reference');
        $reference->setLabel('Reference ')->addFilter('StripTags')->addFilter('StringTrim');

        $deductionAmount = new Application_Form_Element_Money('deduction_amount', ['long' => true]);
        $deductionAmount->setLabel('Deduction Amount ');

        $transactionFee = new Application_Form_Element_Money('transaction_fee', ['long' => true]);
        $transactionFee->setLabel('Transaction Fee ');

        $terms = new Zend_Form_Element_Text('terms');
        $terms->setLabel('Terms ')->addValidator('Int', true, ['messages' => 'Entered value is invalid']);

        $amount = new Application_Form_Element_Money('amount', ['long' => true]);
        $amount->setLabel('Original Amount ')->setAttrib('readonly', 'readonly');

        $recurring = new Zend_Form_Element_Checkbox('recurring');
        $recurring->setLabel('Recurring ')->setAttrib('readonly', 'readonly');

        $billingCycleModel = new CyclePeriod();
        $billingCycle = new Zend_Form_Element_Select('billing_cycle_id');
        $billingCycle->setLabel('Frequency * ')->addmultiOptions(
            $billingCycleModel->getResource()->getOptions()
        )->setAttrib('readonly', 'readonly');

        $weekDay = new Zend_Form_Element_Text('week_day');
        $weekDay->setLabel('Select Days *')->setAttrib('readonly', 'readonly');

        $secondWeekDay = new Zend_Form_Element_Text('second_week_day');
        $secondWeekDay->setAttrib('readonly', 'readonly')->setLabel('and');

        $weekOffset = new Zend_Form_Element_Select('week_offset');
        $weekOffset->setMultiOptions([0 => '1st', 1 => '2nd'])->setLabel('Select Number of Week * ')->setAttrib(
            'readonly',
            'readonly'
        );

        $firstStartDay = new Zend_Form_Element_Select('first_start_day');
        //        $firstStartDay->setLabel('Select Days of Month ')
        $firstStartDay->setLabel('Select Days *')->addValidator(
            'Int',
            true,
            ['messages' => 'Entered value is invalid']
        )->addValidator(
            'between',
            false,
            ['min' => '1', 'max' => '31', 'messages' => 'Entered value is invalid']
        )->setMultiOptions(
            $this->getDaysOptions()
        )->setAttrib('readonly', 'readonly');

        $secondStartDay = new Zend_Form_Element_Select('second_start_day');
        $secondStartDay->setLabel('and')->addValidator(
            'Int',
            true,
            ['messages' => 'Entered value is invalid']
        )->addValidator(
            'between',
            false,
            ['min' => '1', 'max' => '31', 'messages' => 'Entered value is invalid']
        )->setMultiOptions(
            $this->getDaysOptions()
        )->setAttrib('readonly', 'readonly');

        $eligible = new Zend_Form_Element_Checkbox('eligible');
        $eligible->setLabel('Eligible ')->setAttrib('readonly', 'readonly');

        $reserveAccountVendor = new Zend_Form_Element_Hidden('reserve_account_receiver');

        $reserveAccountVendorTitle = new Zend_Form_Element_Text('reserve_account_receiver_title');
        $reserveAccountVendorTitle->setLabel('Reserve Account * ')->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setAttrib('readonly', 'readonly');

        $disbursementDate = new Zend_Form_Element_Text('disbursement_date');
        $disbursementDate->setLabel('Disbursement Date ')->addFilter('StripTags')->addFilter('StringTrim')->setAttrib(
            'readonly',
            'readonly'
        );

        $balance = new Application_Form_Element_Money('balance', ['long' => true]);
        $balance->setLabel('Remaining Balance')->setAttrib('readonly', 'readonly');

        $currentAmount = new Application_Form_Element_Money('adjusted_balance', ['long' => true]);
        $currentAmount->setLabel('Current Amount')->setAttrib('placeholder', "―");

        $approvedDatetime = new Zend_Form_Element_Text('approved_datetime');
        $approvedDatetime->setLabel('Approved Date ')->addFilter('StripTags')->addFilter('StringTrim')->setAttrib(
            'readonly',
            'readonly'
        );

        $approvedBy = new Zend_Form_Element_Hidden('approved_by');
        $approvedByName = new Zend_Form_Element_Text('approved_by_name');
        $approvedByName->setLabel('Approved By ')->setAttrib('readonly', 'readonly');

        $createdDatetime = new Zend_Form_Element_Text('created_datetime');
        $createdDatetime->setLabel('Created Date ')->setAttrib('readonly', 'readonly');

        $createdBy = new Zend_Form_Element_Hidden('created_by');
        $createdByName = new Zend_Form_Element_Text('created_by_name');
        $createdByName->setLabel('Created By ')->setAttrib('readonly', 'readonly');

        $sourceId = new Zend_Form_Element_Text('source_id');
        $sourceId->setLabel('Source ')->setAttrib('readonly', 'readonly');

        $status = new Zend_Form_Element_Hidden('status');

        $statusName = new Zend_Form_Element_Text('status_name');
        $statusName->setLabel('Status ')->setAttrib('readonly', 'readonly');

        $settlementCycleId = new Zend_Form_Element_Select('settlement_cycle_id');
        $settlementCycleId->setLabel('Settlement Cycle')->setAttrib('disabled', 'disabled');

        // $priority = new Application_Form_Element_Hidden('priority');

        $contractorId = new Zend_Form_Element_Hidden('contractor_id');

        $this->addElements(
            [
                $id,
                $providerId,
                $setupId,
                $contractorName,
                $contractorCode,
                $description,
                $powerunitCode,
                $reference,
                $deductionAmount,
                $transactionFee,
                $deductionCode,
                $invoiceDate,
                $invoiceDueDate,
                $department,
                $terms,
                $amount,
                $recurring,
                $billingCycle,
                $firstStartDay,
                $secondStartDay,
                $eligible,
                $reserveAccountVendor,
                $reserveAccountVendorTitle,
                $disbursementDate,
                $balance,
                $currentAmount,
                $approvedDatetime,
                $approvedBy,
                $approvedByName,
                $createdDatetime,
                $createdBy,
                $createdByName,
                $sourceId,
                $statusName,
                $settlementCycleId,
                $status,
                $contractorId,
                // $priority,
                $weekDay,
                $secondWeekDay,
                $weekOffset,
            ]
        );

        $this->setDefaultDecorators(
            [
                'provider_id_title',
                'id',
                'deduction_code',
                'contractor_name',
                'contractor_code',
                'category',
                'description',
                'powerunit_code',
                'reference',
                'deduction_amount',
                'transaction_fee',
                // 'priority',
                'invoice_id',
                'invoice_date',
                'department',
                'gl_code',
                'disbursement_code',
                'terms',
                'amount',
                'recurring',
                'billing_cycle_id',
                'first_start_day',
                'second_start_day',
                'eligible',
                'reserve_account_receiver_title',
                'disbursement_date',
                'balance',
                'adjusted_balance',
                'approved_datetime',
                'approved_by_name',
                'created_by_name',
                'created_datetime',
                'source_id',
                'status_name',
                'settlement_cycle_id',
                'week_day',
                'second_week_day',
                'week_offset',
            ]
        );

        if (!User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE)) {
            foreach ($this->getElements() as $element) {
                $element->setAttrib('readonly', 'readonly');
            }
        } else {
            $this->addSubmit('Save');
        }
    }

    public function setupForNewAction()
    {
        $cycleEntity = new Cycle();
        $this->settlement_cycle_id->setMultiOptions(
            $cycleEntity->getCyclePeriods(
                Cycle::ALL_FILTER_TYPE,
                Cycle::ONLY_ACTIVE
            )
        );
    }

    public function setupForEditAction()
    {
        $cycleEntity = new Cycle();
        $cycleEntity->load($this->settlement_cycle_id->getValue());
        $this->status->setValue($cycleEntity->getStatusId());
        if (!$this->source_id->getValue() && !$this->disbursement_date->getValue()) {
            $this->disbursement_date->setValue($this->changeDateFormat($cycleEntity->getDisbursementDate()));
        }
        $this->recurring->setAttrib('readonly', 'readonly');
        $this->getElement('first_start_day')->setAttrib('disabled', 'disabled');
        $this->getElement('billing_cycle_id')->setAttrib('disabled', 'disabled');
        $this->getElement('second_start_day')->setAttrib('disabled', 'disabled');
        $this->getElement('week_day')->setAttrib('disabled', 'disabled');
        $this->getElement('second_week_day')->setAttrib('disabled', 'disabled');
        $this->getElement('week_offset')->setAttrib('disabled', 'disabled');

        if (!is_null($weekDay = $this->week_day->getValue())) {
            $this->week_day->setValue($this->getWeekOptions()[(int) $weekDay]);
        }
        if (!is_null($secondWeekDay = $this->second_week_day->getValue())) {
            $this->second_week_day->setValue($this->getWeekOptions()[(int) $secondWeekDay]);
        }

        if ($this->billing_cycle_id->getValue() == CyclePeriod::MONTHLY_PERIOD_ID) {
            $this->first_start_day->setLabel('Select Days * ');
        }
        if ($this->billing_cycle_id->getValue() != CyclePeriod::SEMI_WEEKLY_PERIOD_ID) {
            //            $this->week_day->setLabel('Select Day of Week ');
            $this->week_day->setLabel('Select Days * ');
        }
        if ($this->status->getValue() >= CycleStatus::PROCESSED_STATUS_ID) {
            $this->amount->setValue(number_format((float) $this->amount->getValue(), 2));
            $this->balance->setValue(number_format((float) $this->balance->getValue(), 2));
        }
        /*if ($this->status->getValue() != CycleStatus::PROCESSED_STATUS_ID) {
            $this->adjusted_balance->setAttrib('readonly', 'readonly');
        }*/
        if ($cycleEntity->getStatusId() >= CycleStatus::APPROVED_STATUS_ID) {
            foreach ($this->getElements() as $element) {
                $element->setAttrib('readonly', 'readonly');
            }
        }

        $this->settlement_cycle_id->setMultiOptions(
            $cycleEntity->getCyclePeriods(
                Cycle::ALL_FILTER_TYPE
            )
        );

        if ($this->source_id->getValue()) {
            $this->source_id->setValue('Import');
            $this->setup_id->setRequired(false);
        } else {
            $this->source_id->setValue('Manual');
        }

        if (!$this->reserve_account_receiver_title->getValue()) {
            $this->reserve_account_receiver_title->setValue(
                (new Application_Model_Entity_Accounts_Reserve_Vendor())->load(
                    $this->reserve_account_receiver->getValue(),
                    'reserve_account_id'
                )->getAccountTitle()
            );
        }
    }

    public function configureForm($post)
    {
        if (isset($post['source_id'])) {
            $this->setup_id->setRequired(false);
        }
        if (isset($post['billing_cycle_id'])) {
            $periodId = $post['billing_cycle_id'];
        } else {
            return $post;
        }

        switch ($periodId) {
            case CyclePeriod::WEEKLY_PERIOD_ID:
            case CyclePeriod::BIWEEKLY_PERIOD_ID:
            case CyclePeriod::SEMI_WEEKLY_PERIOD_ID:
                $this->first_start_day->setRequired(false);
                $this->second_start_day->setRequired(false);
                unset($post['first_start_day']);
                unset($post['second_start_day']);
                break;
            case CyclePeriod::MONTHLY_PERIOD_ID:
                $this->second_start_day->setRequired(false);
                $this->week_day->setRequired(false);
                unset($post['second_start_day']);
                unset($post['week_day']);
                unset($post['second_week_day']);
                break;
            case CyclePeriod::MONTHLY_SEMI_MONTHLY_ID:
            case CyclePeriod::SEMY_MONTHLY_PERIOD_ID:
                $this->week_day->setRequired(false);
                unset($post['week_day']);
                unset($post['second_week_day']);
                break;
        }

        return $post;
    }

    public function configure()
    {
        parent::configure();
        foreach (['first_start_day', 'second_start_day', 'week_day', 'second_week_day', 'billing_cycle_id', 'reserve_account_receiver_title', 'week_offset',] as $element) {
            $this->getElement($element)->getDecorator('Label')->removeOption('requiredSuffix');
        }

        return $this;
    }
}
