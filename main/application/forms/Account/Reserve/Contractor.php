<?php

class Application_Form_Account_Reserve_Contractor extends Application_Form_Base
{
    public function init()
    {
        $this->setName('reserve_account_contractor');
        parent::init();

        $id = new Zend_Form_Element_Hidden('id');
        $accountBalance = new Zend_Form_Element_Hidden('balance');

        $reserveAccountVendorId = new Zend_Form_Element_Hidden('reserve_account_vendor_id');

        $entityId = new Zend_Form_Element_Hidden('entity_id');
        $entityId->setRequired(true);

        $priority = new Zend_Form_Element_Hidden('priority');

        $reserveAccountVendorName = new Zend_Form_Element_Text('reserve_account_vendor_id_title');
        $reserveAccountVendorName->setLabel('Reserve Account')->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setAttrib('href', '#reserve_account_vendor_id_modal')->setAttrib('data-toggle', 'modal');

        $accountName = new Zend_Form_Element_Hidden('account_name');
        //        $accountName->setRequired(true);

        $description = new Zend_Form_Element_Text('description');
        $description->setLabel('Description ')->addFilter('StripTags')->addFilter('StringTrim')
            ->addFilter(new Application_Model_Filter_TruncateString())->setRequired(true);

        $minBalance = new Application_Form_Element_Money('min_balance');
        $minBalance->setLabel('Minimum Balance')->setRequired();

        $contributionAmount = new Application_Form_Element_Money('contribution_amount');
        $contributionAmount->setLabel('Contribution Amount')->setRequired();

        $initialBalance = new Application_Form_Element_Money('initial_balance');
        $initialBalance->setLabel('Initial Balance')->setRequired();

        $allowNegative = new Zend_Form_Element_Checkbox('allow_negative');
        $allowNegative->setLabel('Allow Negative Balance');

        $currentBalance = new Application_Form_Element_Money('current_balance');
        $currentBalance->setLabel('Current Balance')->setRequired();

        $vendorReserveCode = new Zend_Form_Element_Text('vendor_reserve_code');
        $vendorReserveCode->setLabel('Reserve Code ')->addFilter('StripTags')->addFilter('StringTrim');

        $contractorName = new Zend_Form_Element_Text('entity_id_title');
        $contractorName->setLabel('Company')->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setAttrib('href', '#entity_id_modal')->setAttrib('data-toggle', 'modal');

        $vendorId = new Zend_Form_Element_Hidden('vendor_id');
        $vendorIdTitle = new Zend_Form_Element_Text('vendor_id_title');
        $vendorIdTitle->setLabel('Vendor')->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->setAttrib('href', '#vendor_id_modal')->setAttrib('data-toggle', 'modal');

        $reserveAccountId = new Zend_Form_Element_Hidden('reserve_account_id');
        //        $reserveAccountId->setRequired(true);

        $this->addElements(
            [
                $id,
                $reserveAccountVendorId,
                $entityId,
                $priority,
                $contractorName,
                $reserveAccountVendorName,
                $description,
                $allowNegative,
                $minBalance,
                $contributionAmount,
                $initialBalance,
                $currentBalance,
                $accountName,
                $reserveAccountId,
                $accountBalance,
                $vendorId,
                $vendorIdTitle,
                $vendorReserveCode,
            ]
        );
        $this->setDefaultDecorators(
            [
                'reserve_account_vendor_id_title',
                'description',
                'allow_negative',
                'min_balance',
                'contribution_amount',
                'initial_balance',
                'current_balance',
                'vendor_reserve_code',
                'entity_id_title',
                'vendor_id_title',
            ]
        );
        $this->addSubmit('Save');
    }

    public function setupForEditAction()
    {
        $this->entity_id_title->setAttrib('readonly', 'readonly');
        $this->vendor_id_title->setAttrib('readonly', 'readonly');
        $this->reserve_account_vendor_id_title->setAttrib('readonly', 'readonly');
        $this->vendor_reserve_code->setAttrib('readonly', 'readonly');
        $this->initial_balance->setAttrib('readonly', 'readonly');
        $this->current_balance->setAttrib('readonly', 'readonly');

        if (isset($this->readOnly) && $this->readOnly) {
            $this->description->setAttrib('readonly', 'readonly');
            $this->min_balance->setAttrib('readonly', 'readonly');
            $this->contribution_amount->setAttrib('readonly', 'readonly');
        }

        if (!$this->entity_id_title->getValue()) {
            $contractorCompanyName = (new Application_Model_Entity_Entity_Contractor())->load(
                $this->entity_id->getValue(),
                'entity_id'
            )->getCompanyName();
            $this->entity_id_title->setValue($contractorCompanyName);
        }
        if ($raVendorId = $this->reserve_account_vendor_id->getValue()) {
            $reserveAccountVendorEntity = (new Application_Model_Entity_Accounts_Reserve_Vendor())->load(
                $raVendorId,
                'id'
            );
            $reserveAccountVendorEntity->getDefaultValues();
            if (!$this->reserve_account_vendor_id_title->getValue()) {
                $this->reserve_account_vendor_id_title->setValue($reserveAccountVendorEntity->getAccountTitle());
            }
            if (!$this->vendor_id_title->getValue()) {
                $this->vendor_id_title->setValue($reserveAccountVendorEntity->getEntityIdTitle());
            }
            if (!$this->vendor_reserve_code->getValue()) {
                $this->vendor_reserve_code->setValue($reserveAccountVendorEntity->getVendorReserveCode());
            }
        }
    }

    public function setupForNewAction()
    {
        if ($contractor = Application_Model_Entity_Entity::getCurrentEntity()->getCurrentContractor()) {
            if (!$this->entity_id->getValue()) {
                $this->entity_id->setValue($contractor->getEntityId());
                $this->entity_id_title->setValue($contractor->getCompanyName());
            }
        }
        if (!$this->current_balance->getValue()) {
            $this->current_balance->setValue('0.00');
        }
        if (!$this->initial_balance->getValue()) {
            $this->initial_balance->setValue('0.00');
        }
    }

    public function configure()
    {
        if ($this->getElement('id')->getValue()) {
            $this->setupForEditAction();
        } else {
            $this->setupForNewAction();
        }
    }
}
