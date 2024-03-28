<?php

class Application_Form_Account_ContactAddress extends Application_Form_Base
{
    public function init()
    {
        $this->setName('contact');
        parent::init();

        $contacts_data = new Zend_Form_Element_Hidden('contacts_data');
        $contacts_data->setOptions(['name' => 'contacts_data']);

        $this->addElements([$contacts_data]);
        $this->setDefaultDecorators();
    }
}
