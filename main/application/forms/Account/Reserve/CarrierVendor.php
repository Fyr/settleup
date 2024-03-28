<?php

use Application_Model_Entity_Entity_Permissions as Permissions;

class Application_Form_Account_Reserve_CarrierVendor extends Application_Form_Base
{
    public function init()
    {
        $this->setName('reserve_account_carrier_vendor');
        parent::init();

        $id = new Zend_Form_Element_Hidden('id');
        $accountBalance = new Zend_Form_Element_Hidden('balance');

        $entityId = new Zend_Form_Element_Hidden('entity_id');
        $priority = new Zend_Form_Element_Hidden('priority');

        $accountName = new Zend_Form_Element_Text('account_name');
        $accountName->setLabel('Reserve Account ')->addFilter('StripTags')->addFilter('StringTrim')->setRequired(true);

        $description = new Zend_Form_Element_Text('description');
        $description->setLabel('Description ')->addFilter('StripTags')->addFilter('StringTrim')
            ->addFilter(new Application_Model_Filter_TruncateString())->setRequired(true);

        $minBalance = new Application_Form_Element_Money('min_balance');
        $minBalance->setLabel('Minimum Balance');

        $contributionAmount = new Application_Form_Element_Money('contribution_amount');
        $contributionAmount->setLabel('Contribution Amount');

        $initialBalance = new Application_Form_Element_Money('initial_balance');
        $initialBalance->setLabel('Initial Balance');

        $allowNegative = new Zend_Form_Element_Checkbox('allow_negative');
        $allowNegative->setLabel('Allow Negative Balance');

        $currentBalance = new Application_Form_Element_Money('current_balance');
        $currentBalance->setLabel('Current Balance');

        $vendorReserveCode = new Zend_Form_Element_Text('vendor_reserve_code');
        $vendorReserveCode->setLabel('Reserve Account Code ')->addFilter('StripTags')->addFilter('StringTrim');

        $entityName = new Zend_Form_Element_Text('entity_id_title');
        $entityName->setLabel('Vendor')->setRequired(true)->addFilter('StripTags')->addFilter('StringTrim')->setAttrib(
            'href',
            '#entity_id_modal'
        )->setAttrib('data-toggle', 'modal');

        $reserveAccountId = new Zend_Form_Element_Hidden('reserve_account_id');

        $this->addElements(
            [
                $id,
                $entityId,
                $priority,
                $entityName,
                $description,
                $minBalance,
                $accountName,
                $contributionAmount,
                $initialBalance,
                $currentBalance,
                $accountBalance,
                $vendorReserveCode,
                $reserveAccountId,
                $allowNegative,
            ]
        );
        $this->setDefaultDecorators(
            [
                'description',
                'account_name',
                'account_name',
                'allow_negative',
                'min_balance',
                'contribution_amount',
                'initial_balance',
                'current_balance',
                'vendor_reserve_code',
                'entity_id_title',
            ]
        );
        $this->addSubmit('Save');
    }

    public function setupForEditAction()
    {
        if (str_contains((string) $this->_attribs["action"], "edit")) {
            $this->vendor_reserve_code->setAttrib('readonly', 'readonly');
        }
        $this->initial_balance->setAttrib('readonly', 'readonly');
        $this->current_balance->setAttrib('readonly', 'readonly');
        $this->entity_id_title->setAttrib('readonly', 'readonly');

        $entity = (new Application_Model_Entity_Entity())->load($this->entity_id->getValue(), 'id');
        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        $vendorAccountEntity = new Application_Model_Entity_Accounts_Reserve_Vendor();
        $vendorAccountEntity->load($this->reserve_account_id->getValue(), 'reserve_account_id');

        if (!$this->entity_id_title->getValue()) {
            $this->entity_id_title->setValue($entity->getName());
        }
        if (!$this->vendor_reserve_code->getValue()) {
            $this->vendor_reserve_code->setValue($vendorAccountEntity->getVendorReserveCode());
        }
        if (($entity->getEntityTypeId() == Application_Model_Entity_Entity_Type::TYPE_CARRIER && !$user->hasPermission(
            Permissions::RESERVE_ACCOUNT_CARRIER_MANAGE
        )) || ($entity->getEntityTypeId(
        ) == Application_Model_Entity_Entity_Type::TYPE_VENDOR && !$user->hasPermission(
            Permissions::RESERVE_ACCOUNT_VENDOR_MANAGE
        ))) {
            foreach ($this->getElements() as $element) {
                $element->setAttrib('readonly', 'readonly');
                $this->removeElement('submit');
            }
        }
    }

    public function setupForNewAction()
    {
        $this->initial_balance->setAttrib('readonly', 'readonly');
        $this->initial_balance->setValue('0.00');
        $this->current_balance->setAttrib('readonly', 'readonly');
        $this->current_balance->setValue('0.00');
        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        if ($user->isVendor()) {
            if (!$user->hasPermission(Permissions::RESERVE_ACCOUNT_VENDOR_MANAGE) || !$user->hasPermission(
                Permissions::RESERVE_ACCOUNT_VENDOR_VIEW
            )) {
                Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->gotoSimple(
                    'index',
                    'reporting_index'
                );
            }
            $this->entity_id_title->setValue($user->getEntity()->getName());
            $this->entity_id->setvalue($user->getEntity()->getEntityId());
        } else {
            if (!$this->entity_id->getValue()) {
                if ($user->hasPermission(Permissions::RESERVE_ACCOUNT_CARRIER_MANAGE) && $user->hasPermission(
                    Permissions::RESERVE_ACCOUNT_CARRIER_VIEW
                )) {
                    $this->entity_id_title->setValue($user->getSelectedCarrier()->getName());
                    $this->entity_id->setValue($user->getSelectedCarrier()->getEntityId());
                }
            } else {
                if (!$this->entity_id_title->getValue()) {
                    $entityName = (new Application_Model_Entity_Entity())->load(
                        $this->entity_id->getValue(),
                        'id'
                    )->getEntityName();
                    $this->entity_id_title->setValue($entityName);
                }
            }
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
