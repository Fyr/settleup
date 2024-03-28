<?php

class Application_Form_Account_Reserve_Vendor extends Application_Form_Base
{
    public function init()
    {
        $this->setName('reserve_account_vendor');
        parent::init();

        $id = new Zend_Form_Element_Hidden('id');
        $accountBalance = new Zend_Form_Element_Hidden('balance');

        $entityId = new Zend_Form_Element_Hidden('entity_id');
        $entityId->setValue(
            Application_Model_Entity_Entity_Base::getCurrentEntityId()
        )->setRequired(true);

        $accountName = new Zend_Form_Element_Text('account_name');
        $accountName->setLabel('Account Name ')->addFilter('StripTags')->addFilter('StringTrim')->setRequired(true);

        $description = new Zend_Form_Element_Text('description');
        $description->setLabel('Description ')->addFilter('StripTags')->addFilter('StringTrim')
            ->addFilter(new Application_Model_Filter_TruncateString())->setRequired(true);

        $minBalance = new Zend_Form_Element_Text('min_balance');
        $minBalance->setLabel('Minimum Balance ')->addFilter(new Application_Model_Filter_DeleteCommas())->addValidator(
            'between',
            true,
            ['min' => -1_000_000, 'max' => 1_000_000]
        )->addValidator('Float', true, ['messages' => 'Entered value is invalid']);

        $contributionAmount = new Zend_Form_Element_Text('contribution_amount');
        $contributionAmount->setLabel('Contribution Amount ')->addFilter(
            new Application_Model_Filter_DeleteCommas()
        )->addValidator('between', true, ['min' => 0, 'max' => 1_000_000])->addValidator(
            'Float',
            true,
            ['messages' => 'Entered value is invalid']
        );

        $initialBalance = new Zend_Form_Element_Text('initial_balance');
        $initialBalance->setLabel('Initial Balance ')->addFilter(
            new Application_Model_Filter_DeleteCommas()
        )->addValidator('between', true, ['min' => -1_000_000, 'max' => 1_000_000])->addValidator(
            'Float',
            true,
            ['messages' => 'Entered value is invalid']
        )->setAttrib('readonly', 'readonly');

        $currentBalance = new Zend_Form_Element_Text('current_balance');
        $currentBalance->setLabel('Current Balance ')->addFilter(
            new Application_Model_Filter_DeleteCommas()
        )->addValidator('between', true, ['min' => -1_000_000, 'max' => 1_000_000])->addValidator(
            'Float',
            true,
            ['messages' => 'Entered value is invalid']
        );

        $disbursementCode = new Zend_Form_Element_Text('disbursement_code');
        $disbursementCode->setLabel('Disbursement Code ')->addFilter('StripTags')->addFilter('StringTrim');

        $vendorName = new Zend_Form_Element_Text('entity_id_title');
        $vendorName->setLabel('Vendor name')->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setAttrib('href', '#entity_id_modal')->setAttrib('data-toggle', 'modal');

        $vendorReserveCode = new Zend_Form_Element_Text('vendor_reserve_code');
        $vendorReserveCode->setLabel('Vendor Reserve Code ')->addFilter('StripTags')->addFilter('StringTrim');

        $reserveAccountId = new Zend_Form_Element_Hidden('reserve_account_id');

        $this->addElements(
            [
                $id,
                $entityId,
                $vendorName,
                $accountName,
                $description,
                $minBalance,
                $contributionAmount,
                $initialBalance,
                $currentBalance,
                $disbursementCode,
                $vendorReserveCode,
                $reserveAccountId,
                $accountBalance,
            ]
        );

        $this->setDefaultDecorators(
            [
                'account_name',
                'description',
                'min_balance',
                'contribution_amount',
                'initial_balance',
                'current_balance',
                'disbursement_code',
                'entity_id_title',
                'vendor_reserve_code',
            ]
        );
        $this->addSubmit('Submit');
    }
}
