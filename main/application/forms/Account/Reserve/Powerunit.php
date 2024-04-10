<?php

use Application_Model_Entity_Powerunit_Powerunit as Powerunit;
use Application_Model_Entity_System_ReserveAccountType as AccountType;

class Application_Form_Account_Reserve_Powerunit extends Application_Form_Base
{
    public function init()
    {
        $this->setName('reserve_account');
        parent::init();

        $powerunitIdFieldOptions = $this->getPowerunitIdFieldOptions();

        $id = new Zend_Form_Element_Hidden('id');
        $accountBalance = new Zend_Form_Element_Hidden('balance');

        // $reserveAccountVendorId = new Zend_Form_Element_Hidden('reserve_account_vendor_id');

        $entityId = new Zend_Form_Element_Hidden('entity_id');

        $priority = new Zend_Form_Element_Hidden('priority');

        // $reserveAccountVendorName = new Zend_Form_Element_Text('reserve_account_vendor_id_title');
        // $reserveAccountVendorName->setLabel('Reserve Account')->setRequired(true)->addFilter('StripTags')->addFilter(
        //     'StringTrim'
        // )->setAttrib('href', '#reserve_account_vendor_id_modal')->setAttrib('data-toggle', 'modal');

        $accountName = (new Zend_Form_Element_Text('account_name'))
            ->setLabel('Name')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setRequired(true);

        $code = new Zend_Form_Element_Text('code');
        $code->setLabel('Code')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator(
                'Db_NoRecordExists',
                false,
                [
                    'table' => 'reserve_account',
                    'field' => 'code',
                    'exclude' => [
                        'field' => 'entity_id',
                        'value' => (int)Zend_Controller_Front::getInstance()->getRequest()->get('entity_id'),
                    ],
                    'messages' => 'Sorry, it looks like "%value%" belongs to an existing Reserve Account.',
                ]
            );

        // $powerunitId = new Zend_Form_Element_Text('entity_id_title');
        // $powerunitId->setLabel('Powerunit Code')
        //     ->setRequired(true)
        //     ->addFilter('StripTags')
        //     ->addFilter('StringTrim')
        //     ->setAttrib('href', '#entity_id_modal')
        //     ->setAttrib('data-toggle', 'modal');

        // $powerunitId2 = new Zend_Form_Element_Text('powerunit_id');
        // $powerunitId2->setLabel('Powerunit Code')
        //     ->setRequired(true)
        //     ->addFilter('StripTags')
        //     ->addFilter('StringTrim')
        //     ->setAttrib('href', '#powerunit_id_modal')
        //     ->setAttrib('data-toggle', 'modal');

        $powerunitId = (new Zend_Form_Element_Select('powerunit_id'))
            ->setLabel('Powerunit Code')
            ->addMultiOptions($powerunitIdFieldOptions)
            ->setRequired();

        $description = new Zend_Form_Element_Text('description');
        $description
            ->setLabel('Description ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addFilter(new Application_Model_Filter_TruncateString());

        $accountType = (new Zend_Form_Element_Select('account_type'))
            ->setLabel('Account Type ')
            ->setRequired()
            ->addMultiOptions(
                [
                    AccountType::ESCROW_ACCOUNT => 'Escrow',
                    AccountType::MAINTENANCE_ACCOUNT => 'Maintenance',
                ]
            );

        $establishedDate = (new Zend_Form_Element_Text('start_date'))
            ->setLabel('Established Date')
            ->addValidator('DateDatetime')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $minBalance = new Application_Form_Element_Money('min_balance');
        $minBalance->setLabel('Minimum Balance');

        $contributionAmount = new Application_Form_Element_Money('contribution_amount');
        $contributionAmount->setLabel('Contribution Amount')->setRequired();

        $initialBalance = new Application_Form_Element_Money('initial_balance');
        $initialBalance->setLabel('Initial Balance')->setRequired();

        $allowNegative = new Zend_Form_Element_Checkbox('allow_negative');
        $allowNegative->setLabel('Allow Negative Balance');

        $currentBalance = new Application_Form_Element_Money('current_balance');
        $currentBalance->setLabel('Current Balance')->setRequired();

        $accumulatedInterest = (new Application_Form_Element_Money('accumulated_interest'))
            ->setLabel('Accumulated Interest');

        // $vendorReserveCode = new Zend_Form_Element_Text('vendor_reserve_code');
        // $vendorReserveCode->setLabel('Reserve Code ')->addFilter('StripTags')->addFilter('StringTrim');

        // $vendorId = new Zend_Form_Element_Hidden('vendor_id');
        // $vendorIdTitle = new Zend_Form_Element_Text('vendor_id_title');
        // $vendorIdTitle->setLabel('Vendor')->setRequired(true)->addFilter('StripTags')->addFilter(
        //     'StringTrim'
        // )->setAttrib('href', '#vendor_id_modal')->setAttrib('data-toggle', 'modal');

        $reserveAccountId = new Zend_Form_Element_Hidden('reserve_account_id');
        //        $reserveAccountId->setRequired(true);

        $this->addElements(
            [
                $id,
                // $reserveAccountVendorId,
                $entityId,
                $priority,
                $powerunitId,
                // $powerunitId2,
                $accountBalance,
                $code,
                // $reserveAccountVendorName,
                $description,
                $accountType,
                $establishedDate,
                $allowNegative,
                $minBalance,
                $contributionAmount,
                $initialBalance,
                $currentBalance,
                $accountName,
                $reserveAccountId,
                $accountBalance,
                $accumulatedInterest,
                // $vendorId,
                // $vendorIdTitle,
                // $vendorReserveCode,
            ]
        );
        $this->setDefaultDecorators(
            [
                // 'reserve_account_vendor_id_title',
                'account_name',
                'code',
                // 'entity_id_title',
                'powerunit_id',
                'description',
                'account_type',
                'start_date',
                'allow_negative',
                'min_balance',
                'contribution_amount',
                'initial_balance',
                'current_balance',
                'accumulated_interest',
                // 'vendor_reserve_code',
                // 'vendor_id_title',
            ]
        );
        $this->addSubmit('Save');
    }

    public function setupForEditAction()
    {
        // $this->entity_id_title->setAttrib('readonly', 'readonly');
        // $this->vendor_id_title->setAttrib('readonly', 'readonly');
        // $this->reserve_account_vendor_id_title->setAttrib('readonly', 'readonly');
        // $this->vendor_reserve_code->setAttrib('readonly', 'readonly');
        $this->initial_balance->setAttrib('readonly', 'readonly');
        $this->current_balance->setAttrib('readonly', 'readonly');
        $this->accumulated_interest->setAttrib('readonly', 'readonly');

        if (isset($this->readOnly) && $this->readOnly) {
            $this->description->setAttrib('readonly', 'readonly');
            $this->min_balance->setAttrib('readonly', 'readonly');
            $this->contribution_amount->setAttrib('readonly', 'readonly');
        }

        // if (!$this->entity_id_title->getValue()) {
        //     $powerunitCompanyName = (new Application_Model_Entity_Powerunit_Powerunit())->load(
        //         $this->entity_id->getValue(),
        //         'entity_id'
        //     )->getCompanyName();
        //     $this->entity_id_title->setValue($powerunitCompanyName);
        // }
        // if ($raVendorId = $this->reserve_account_vendor_id->getValue()) {
        //     $reserveAccountVendorEntity = (new Application_Model_Entity_Accounts_Reserve_Vendor())->load(
        //         $raVendorId,
        //         'id'
        //     );
        //     $reserveAccountVendorEntity->getDefaultValues();
        //     if (!$this->reserve_account_vendor_id_title->getValue()) {
        //         $this->reserve_account_vendor_id_title->setValue($reserveAccountVendorEntity->getAccountTitle());
        //     }
        //     if (!$this->vendor_id_title->getValue()) {
        //         $this->vendor_id_title->setValue($reserveAccountVendorEntity->getEntityIdTitle());
        //     }
        //     if (!$this->vendor_reserve_code->getValue()) {
        //         $this->vendor_reserve_code->setValue($reserveAccountVendorEntity->getVendorReserveCode());
        //     }
        // }
    }

    public function setupForNewAction()
    {
        // if ($powerunit = Application_Model_Entity_Entity::getCurrentEntity()->getCurrentPowerunit()) {
        //     if (!$this->entity_id->getValue()) {
        //         $this->entity_id->setValue($powerunit->getEntityId());
        //         $this->entity_id_title->setValue($powerunit->getCompanyName());
        //     }
        // }
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

    public function getPowerunitIdFieldOptions(): array
    {
        // pull a list of active power units filtered by current division and settlement group
        // pull state from a current user object
        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        $settlementGroupId = $user->getLastSelectedSettlementGroup();

        $powerunitEntity = new Powerunit();
        $powerunits = $powerunitEntity->getCollection()
            ->addSettlementGroupFilter($settlementGroupId)
            ->addNonDeletedFilter()
        ;
        $powerunitIdFieldOptions = [];

        foreach ($powerunits->getItems() as $powerunit) {
            $powerunitIdFieldOptions[strval($powerunit->getId())] = $powerunit->getCode();
        }

        return $powerunitIdFieldOptions;
    }
}
