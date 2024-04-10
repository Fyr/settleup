<?php

use Application_Model_Entity_Entity_Carrier as Carrier;

class Application_Form_Account_Escrow extends Application_Form_Base
{
    protected $useCarrierKey = true;
    /** @var Carrier */
    protected $carrier;

    protected function getCarrierKey()
    {
        if ($this->getCarrier()) {
            return $this->getCarrier()->getEntityId();
        }

        return false;
    }

    /**
     * @return $this
     */
    public function setCarrier(Carrier $carrier)
    {
        $this->carrier = $carrier;

        return $this;
    }

    /**
     * @return Carrier
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    public function init()
    {
        $this->setName('escrow_account');
        parent::init();

        $id = new Application_Form_Element_Hidden('id');

        $carrierId = new Application_Form_Element_Hidden('carrier_id');

        $carrierTitle = new Zend_Form_Element_Text('carrier_title');
        $carrierTitle->setLabel('Division')->setAttrib('disabled', 'disabled');

        $escrowAccountHolder = (new Zend_Form_Element_Text('escrow_account_holder'))->setLabel(
            'Escrow Account Holder'
        )->setRequired();

        $holderFederalTaxId = (new Zend_Form_Element_Text('holder_federal_tax_id'))->setLabel(
            'Holder Federal Tax ID'
        )->setRequired()->addFilter('StripTags')->addFilter('StringTrim')->addValidator(
            'Regex',
            false,
            [
                'pattern' => '/\d{2}\-\d{7}/',
                'messages' => 'Invalid format! Example: ##-#######',
            ]
        );

        $holderAddress = (new Zend_Form_Element_Text('holder_address'))->setLabel('Holder Address 1');

        $holderAddress2 = (new Zend_Form_Element_Text('holder_address_2'))->setLabel('Holder Address 2');

        $holderCity = (new Zend_Form_Element_Text('holder_city'))->setLabel('Holder City');

        $holderState = (new Zend_Form_Element_Text('holder_state'))->setLabel('Holder State');

        $holderZip = (new Zend_Form_Element_Text('holder_zip'))->setLabel('Holder Zip');

        $nextCheckNumber = (new Zend_Form_Element_Text('next_check_number'))->setLabel(
            'Next Check Number'
        )->addValidators([
                ['name' => 'Int', true, ['messages' => 'Entered value is invalid']],
                ['name' => 'LessThan', 'max' => 4_000_000_000, 'options' => ['max' => 4_000_000_000]],
            ]);

        $this->addElements(
            [
                $id,
                $carrierId,
                $carrierTitle,
                $escrowAccountHolder,
                $holderFederalTaxId,
                $escrowAccountHolder,
                $nextCheckNumber,
                $holderAddress,
                $holderAddress2,
                $holderCity,
                $holderState,
                $holderZip,
            ]
        );

        $this->setDefaultDecorators(
            [
                'carrier_title',
                'escrow_account_holder',
                'holder_federal_tax_id',
                'next_check_number',
                'holder_address',
                'holder_address_2',
                'holder_city',
                'holder_state',
                'holder_zip',
            ]
        );
    }

    public function setupForEditAction()
    {
        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        $carrier = Carrier::staticLoad($this->carrier_id->getValue(), 'entity_id');
        $this->carrier_title->setValue($carrier->getName());
        if ($user->isManager()) {
            $this->escrow_account_holder->setAttrib('disabled', 'disabled')->setRequired(false);
            $this->holder_federal_tax_id->setAttrib('disabled', 'disabled')->setRequired(false);
            $this->holder_address->setAttrib('disabled', 'disabled')->setRequired(false);
            $this->holder_address_2->setAttrib('disabled', 'disabled')->setRequired(false);
            $this->holder_city->setAttrib('disabled', 'disabled')->setRequired(false);
            $this->holder_state->setAttrib('disabled', 'disabled')->setRequired(false);
            $this->holder_zip->setAttrib('disabled', 'disabled')->setRequired(false);
            $this->next_check_number->setAttrib('disabled', 'disabled')->setRequired(false);
        }
    }

    public function setupForNewAction()
    {
        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        $carrier = Carrier::staticLoad($this->carrier_id->getValue(), 'entity_id');
        if ($carrier->getId()) {
            $this->carrier_title->setValue($carrier->getName());
        } else {
            $this->carrier_title->setValue($user->getSelectedCarrier()->getName());
        }
        if ($user->isManager()) {
            $this->escrow_account_holder->setAttrib('disabled', 'disabled')->setRequired(false);
            $this->holder_federal_tax_id->setAttrib('disabled', 'disabled')->setRequired(false);
            $this->holder_address->setAttrib('disabled', 'disabled')->setRequired(false);
            $this->holder_address_2->setAttrib('disabled', 'disabled')->setRequired(false);
            $this->holder_city->setAttrib('disabled', 'disabled')->setRequired(false);
            $this->holder_state->setAttrib('disabled', 'disabled')->setRequired(false);
            $this->holder_zip->setAttrib('disabled', 'disabled')->setRequired(false);
            $this->next_check_number->setAttrib('disabled', 'disabled')->setRequired(false);
        }
    }
}
