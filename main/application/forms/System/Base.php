<?php

class Application_Form_System_Base extends Application_Form_Base
{
    public function init()
    {
        $this->setName('system_value');
        parent::init();

        $id = new Zend_Form_Element_Text('id');
        $id->setLabel('Id ')->addValidator('Int', true, ['messages' => 'Entered value is invalid'])->setRequired(true);

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title ')->addFilter('StripTags')->addFilter('StringTrim')->setRequired(true);

        $this->addElements([$id, $title]);

        $this->setDefaultDecorators(['id', 'title']);
        $this->addSubmit('Save');
    }
}
