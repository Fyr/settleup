<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_Payments_TaxableType as TaxableType;
use Application_Model_Entity_Settlement_Cycle as Cycle;
use Application_Model_Entity_System_CyclePeriod as CyclePeriod;
use Application_Model_Entity_System_SettlementCycleStatus as CycleStatus;

class Application_Form_Payments_Payment extends Application_Form_Base
{
    use Application_Form_WeekOptionsTrait;

    public function init()
    {
        $fieldNames = User::getCurrentUser()->getSelectedCarrier()->getCustomFieldNames();
        $this->setName('payments');
        parent::init();

        $id = new Zend_Form_Element_Hidden('id');

        $setupId = new Zend_Form_Element_Hidden('setup_id');
        $setupId->setRequired(true);

        $carrierId = new Zend_Form_Element_Hidden('carrier_id');

        $paymentCode = new Zend_Form_Element_Text('payment_code');
        $paymentCode->setLabel($fieldNames->getPaymentCode())->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setAttrib('readonly', 'readonly');

        $carrierPaymentCode = new Zend_Form_Element_Text('carrier_payment_code');
        $carrierPaymentCode->setLabel($fieldNames->getCarrierPaymentCode())->addFilter('StripTags')->addFilter(
            'StringTrim'
        );

        $terms = new Zend_Form_Element_Text('terms');
        $terms->setLabel('Terms ')->addValidator('Int', true, ['messages' => 'Entered value is invalid']);

        $disbursementCode = new Zend_Form_Element_Text('disbursement_code');
        $disbursementCode->setLabel($fieldNames->getDisbursementCode())->addFilter('StripTags')->addFilter(
            'StringTrim'
        );

        $recurring = new Zend_Form_Element_Checkbox('recurring');
        $recurring->setLabel('Recurring ')->setAttrib('readonly', 'readonly');

        $cycleEntity = new CyclePeriod();
        $billingCycle = new Zend_Form_Element_Select('billing_cycle_id');
        $billingCycle->setLabel('Frequency * ')->setMultiOptions(
            $cycleEntity->getResource()->getOptions()
        )->setAttrib('readonly', 'readonly');

        $weekDay = new Zend_Form_Element_Text('week_day');
        $weekDay->setLabel('Select Days * ')->setAttrib('readonly', 'readonly');

        $secondWeekDay = new Zend_Form_Element_Text('second_week_day');
        $secondWeekDay->setLabel('and')->setAttrib('readonly', 'readonly');

        $weekOffset = new Zend_Form_Element_Select('week_offset');
        $weekOffset->setMultiOptions([0 => '1st', 1 => '2nd'])->setLabel('Select Number of Week * ')->setAttrib(
            'readonly',
            'readonly'
        );

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
        )->setAttrib('readonly', 'readonly');

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
        )->setAttrib('readonly', 'readonly');

        $contractorName = new Zend_Form_Element_Text('contractor_name');
        $contractorName->setLabel('Company ')->addFilter('StripTags')->addFilter('StringTrim')->setAttrib(
            'readonly',
            'readonly'
        );
        $contractorCode = new Zend_Form_Element_Text('contractor_code');
        $contractorCode->setLabel('Contractor Code ')->addFilter('StripTags')->addFilter('StringTrim')->setAttrib(
            'readonly',
            'readonly'
        );

        $description = new Zend_Form_Element_Text('description');
        $description->setLabel($fieldNames->getDescription())->addFilter('StripTags')->addFilter(
            'StringTrim'
        );

        $invoice = new Zend_Form_Element_Text('invoice');
        $invoice->setLabel($fieldNames->getInvoice())->addFilter('StripTags')->addFilter('StringTrim');

        $invoiceDate = new Zend_Form_Element_Text('invoice_date');
        $invoiceDate->setLabel($fieldNames->getInvoiceDate())->addValidator('DateDatetime')->addFilter(
            'StripTags'
        )->addFilter('StringTrim')->setRequired(true);

        $invoiceDueDate = new Application_Form_Element_Hidden('invoice_due_date');
        //        $invoiceDueDate->setLabel('Invoice Due Date ')
        //            ->addFilter('StripTags')
        //            ->addFilter('StringTrim')
        //            ->setRequired(true);

        $department = new Zend_Form_Element_Text('department');
        $department->setLabel($fieldNames->getDepartment())->addFilter('StripTags')->addFilter('StringTrim');

        $glCode = new Zend_Form_Element_Text('gl_code');
        $glCode->setLabel($fieldNames->getGlCode())->addFilter('StripTags')->addFilter('StringTrim');

        $quantity = new Zend_Form_Element_Text('quantity');
        $quantity->setLabel('Quantity ')->setRequired(true)->addFilter(
            new Application_Model_Filter_DeleteCommas()
        )->addValidator('between', true, ['min' => -1_000_000, 'max' => 1_000_000])->addValidator(
            'Int',
            true,
            ['messages' => 'Entered value is invalid']
        );

        $rate = new Application_Form_Element_Money('rate');
        $rate->setLabel('Rate ')->setRequired(true);

        $amount = new Zend_Form_Element_Text('amount');
        $amount->setLabel('Amount ')->addValidator(
            'Float',
            true,
            ['messages' => 'Entered value is invalid']
        )->setAttrib('class', 'mask-money')->addFilter(new Application_Model_Filter_DeleteCommas());

        $balance = new Zend_Form_Element_Hidden('balance');

        $checkId = new Zend_Form_Element_Text('check_id');
        $checkId->setLabel('Check Id ')->addFilter('StripTags')->addFilter('StringTrim');

        $disbursementDate = new Zend_Form_Element_Text('disbursement_date');
        $disbursementDate->setLabel('Disbursement Date ')->addFilter('StripTags')->addFilter('StringTrim');

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
        $powerunitCode = new Zend_Form_Element_Text('powerunit_code');
        $powerunitCode->setLabel('Power Unit')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('readonly', 'readonly');

        $shipment = (new Zend_Form_Element_Text('shipment'))
            ->setLabel('Shipment')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $shipmentCompleteDate = new Zend_Form_Element_Text('shipment_complete_date');
        $shipmentCompleteDate->setLabel('Shipment Complete Date')
            ->addValidator('DateDatetime')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $driver = new Zend_Form_Element_Text('driver');
        $driver->setLabel('Driver')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $reference = new Zend_Form_Element_Text('reference');
        $reference->setLabel('Reference')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $taxable = (new Zend_Form_Element_Select('taxable'))
            ->setLabel('Taxable')
            ->setMultiOptions((new TaxableType())->getTaxableOptions());

        $loadedMiles = new Zend_Form_Element_Text('loaded_miles');
        $loadedMiles->setLabel('Loaded Miles')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $emptyMiles = new Zend_Form_Element_Text('empty_miles');
        $emptyMiles->setLabel('Empty Miles')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $originCity = new Zend_Form_Element_Text('origin_city');
        $originCity->setLabel('Origin City/State')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $destinationCity = new Zend_Form_Element_Text('destination_city');
        $destinationCity->setLabel('Destination City/State')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');



        $this->addElement($shipmentCompleteDate);

        $settlementCycleId = (new Zend_Form_Element_Select('settlement_cycle_id'))->setLabel('Settlement Cycle');

        $contractorId = new Zend_Form_Element_Hidden('contractor_id');

        $this->addElements(
            [
                $amount,
                $approvedBy,
                $approvedByName,
                $approvedDatetime,
                $balance,
                $billingCycle,
                $carrierId,
                $carrierPaymentCode,
                $checkId,
                $contractorCode,
                $contractorName,
                $createdBy,
                $createdByName,
                $createdDatetime,
                $department,
                $description,
                $destinationCity,
                $disbursementCode,
                $disbursementDate,
                $driver,
                $emptyMiles,
                $firstStartDay,
                $glCode,
                $id,
                $invoice,
                $invoiceDate,
                $invoiceDueDate,
                $loadedMiles,
                $originCity,
                $paymentCode,
                $powerunitCode,
                $quantity,
                $rate,
                $recurring,
                $reference,
                $secondStartDay,
                $secondWeekDay,
                $settlementCycleId,
                $setupId,
                $shipment,
                $shipmentCompleteDate,
                $sourceId,
                $status,
                $statusName,
                $taxable,
                $terms,
                $weekDay,
                $weekOffset,
            ]
        );

        $this->setDefaultDecorators(
            [
                'amount',
                'approved_by_name',
                'approved_datetime',
                'billing_cycle_id',
                'carrier_payment_code',
                'check_id',
                'contractor_code',
                'contractor_name',
                'created_by_name',
                'created_datetime',
                'department',
                'description',
                'destination_city',
                'disbursement_code',
                'disbursement_date',
                'driver',
                'empty_miles',
                'first_start_day',
                'gl_code',
                'invoice_date',
                'invoice',
                'loaded_miles',
                'origin_city',
                'payment_code',
                'powerunit_code',
                'quantity',
                'rate',
                'recurring',
                'reference',
                'second_start_day',
                'second_week_day',
                'settlement_cycle_id',
                'shipment',
                'shipment_complete_date',
                'source_id',
                'status_name',
                'taxable',
                'terms',
                'week_day',
                'week_offset',
            ]
        );

        $this->addSubmit('Save');
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
        $this->checkPermissions();
    }

    public function setupForEditAction()
    {
        $this->getElement('billing_cycle_id')->setAttrib('disabled', 'disabled');
        $this->getElement('amount')->setAttrib('readonly', 'readonly');
        $this->getElement('source_id')->setAttrib('readonly', 'readonly');
        $this->getElement('payment_code')->setAttrib('readonly', 'readonly');
        $this->getElement('disbursement_date')->setAttrib('disabled', 'disabled');
        $this->getElement('settlement_cycle_id')->setAttrib('disabled', 'disabled');
        $this->getElement('first_start_day')->setAttrib('disabled', 'disabled');
        $this->getElement('second_start_day')->setAttrib('disabled', 'disabled');
        $this->getElement('week_day')->setAttrib('disabled', 'disabled');
        $this->getElement('second_week_day')->setAttrib('disabled', 'disabled');
        $this->getElement('week_offset')->setAttrib('disabled', 'disabled');

        if ($this->billing_cycle_id->getValue() == CyclePeriod::MONTHLY_PERIOD_ID) {
            //            $this->first_start_day->setLabel('Select Day of Month ');
            $this->first_start_day->setLabel('Select Days * ');
        }
        if ($this->billing_cycle_id->getValue() != CyclePeriod::SEMI_WEEKLY_PERIOD_ID) {
            //            $this->week_day->setLabel('Select Day of Weekk ');
            $this->week_day->setLabel('Select Days * ');
        }

        if (!is_null($weekDay = $this->week_day->getValue())) {
            $this->week_day->setValue($this->getWeekOptions()[(int)$weekDay]);
        }
        if (!is_null($secondWeekDay = $this->second_week_day->getValue())) {
            $this->second_week_day->setValue($this->getWeekOptions()[(int)$secondWeekDay]);
        }

        $cycleEntity = new Cycle();
        $cycleEntity->load($this->settlement_cycle_id->getValue());
        if (!$this->source_id->getValue() && !$this->disbursement_date->getValue()) {
            $this->disbursement_date->setValue($this->changeDateFormat($cycleEntity->getDisbursementDate()));
        }

        if ($this->status->getValue() >= Application_Model_Entity_System_PaymentStatus::PROCESSED_STATUS) {
            $this->amount->setValue(number_format($this->amount->getValue(), 2));
            $this->balance->setValue(number_format($this->balance->getValue(), 0));
        }

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
        $this->checkPermissions();
    }

    public function checkPermissions()
    {
        if (!User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE)) {
            foreach ($this->getElements() as $element) {
                $element->setAttrib('readonly', 'readonly');
            }
            $this->removeElement('submit');
        }
    }

    public function configureForm($post)
    {
        if (isset($post['billing_cycle_id'])) {
            $periodId = $post['billing_cycle_id'];
        } else {
            $payment = new Application_Model_Entity_Payments_Payment();
            $payment->load($post['id']);
            $periodId = $payment->getBillingCycleId();
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
        if (isset($post['source_id'])) {
            $this->setup_id->setRequired(false);
        }

        return $post;
    }

    public function isValid($data)
    {
        if (isset($data['id'])) {
            $this->getElement('first_start_day')->setRequired(false);
            $this->getElement('second_start_day')->setRequired(false);
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
                'week_offset',
            ] as $element
        ) {
            $this->getElement($element)->getDecorator('Label')->removeOption('requiredSuffix');
        }

        return $this;
    }
}
