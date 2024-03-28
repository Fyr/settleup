<?php

class Application_Form_System_SystemValues extends Application_Form_Base
{
    public function init()
    {
        $this->setName('system_value');
        parent::init();

        $id = new Zend_Form_Element_Text('id');
        $id->setLabel('Id ')->addValidator('Int', true, ['messages' => 'Entered value is invalid'])->setAttrib(
            'readonly',
            'readonly'
        );

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title ')->addFilter('StripTags')->addFilter('StringTrim')->setRequired(true);

        $value = new Zend_Form_Element_Text('value');
        $value->setLabel('Value ')->addValidator('Int', true, ['messages' => 'Entered value is invalid'])->setRequired(
            true
        );

        $this->addElements([$id, $title, $value]);

        $this->setDefaultDecorators(['id', 'title', 'value']);
        $this->addSubmit('Save');
    }
}
