<?php

class Application_Form_Account_Contact extends Application_Form_Base
{
    public function init()
    {
        $this->setName('contact');
        parent::init();

        $id = new Application_Form_Element_Hidden('id');

        $entityId = new Application_Form_Element_Hidden('entity_id');

        $userId = new Application_Form_Element_Hidden('user_id');

        $contactType = new Application_Form_Element_Hidden('contact_type');

        $value = new Application_Form_Element_Text('value');
        $value->addFilter('StripTags')->addFilter('StringTrim');
        $title = new Application_Form_Element_Hidden('title');

        $deleted = new Application_Form_Element_Hidden('deleted');

        $this->addElements([$id, $entityId, $userId, $contactType, $value, $title, $deleted]);
        $this->setDefaultDecorators(['value']);
    }
}
