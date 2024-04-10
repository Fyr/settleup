<?php

use Application_Model_Entity_Entity_Permissions as Permissions;

class Application_Form_Entity_Carrier extends Application_Form_Base
{
    use Application_Form_ContactSubformTrait;

    public function init()
    {
        $this->setName('carrier');
        parent::init();
        $currentUser = Application_Model_Entity_Accounts_User::getCurrentUser();

        $id = new Application_Form_Element_Hidden('id');

        $entityId = new Application_Form_Element_Hidden('entity_id');

        $taxId = new Zend_Form_Element_Text('tax_id');
        $taxId->setLabel('Federal Tax ID ')->setRequired(true)->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->addValidator(
            'Regex',
            false,
            [
                'pattern' => '/\d{2}\-\d{7}/',
                'messages' => 'Invalid format! Example: ##-#######',
            ]
        );

        $shortCode = new Zend_Form_Element_Text('short_code');
        $shortCode->setLabel('Code')->setRequired()->addFilter('StripTags')->addFilter('StringTrim');

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Name ')->setRequired(true)->addFilter('StripTags')->addFilter('StringTrim');

        $contact = new Zend_Form_Element_Text('contact');
        $contact->setLabel('Contact ')->setRequired(true)->addFilter('StripTags')->addFilter('StringTrim');

        $terms = new Zend_Form_Element_Text('terms');
        $terms->setLabel('Terms ')->setRequired(true)->addValidator(
            'Int',
            true,
            ['messages' => 'Entered value is invalid']
        );

        $this->addElements(
            [$id, $entityId, $taxId, $shortCode, $name, $terms, $contact]
        );

        $this->setDefaultDecorators(
            ['tax_id', 'short_code', 'name', 'contact', 'terms']
        );

        if (!$currentUser->hasPermission(Permissions::CARRIER_MANAGE) || !$currentUser->isAdminOrSuperAdmin()) {
            foreach ($this->getElements() as $element) {
                $element->setAttrib('readonly', 'readonly');
            }
        } else {
            $this->addSubmit('Save');
        }

        if ($currentUser->isSuperAdmin()) {
            $createContractorType = new Zend_Form_Element_Select('create_contractor_type');
            $createContractorType->setLabel('Contractor User ')->setMultiOptions([
                Application_Model_Entity_Entity_Carrier::AUTO_CREATE_CONTRACTOR_USER => 'Auto create',
                Application_Model_Entity_Entity_Carrier::MANUALLY_CREATE_CONTRACTOR_USER => 'Manually create',
            ]);
            $this->addElement($createContractorType);
            $this->setDefaultDecorators(['create_contractor_type']);
        }
    }
}
