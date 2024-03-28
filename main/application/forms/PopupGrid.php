<?php

class Application_Form_PopupGrid extends Zend_Form
{
    public function init()
    {
        $this->setName('popup_selector');
        $id = new Zend_Form_Element_Hidden('idField');
        $id->addFilter('Int');

        $data = new Zend_Form_Element_Text('titleField');
        $data->setLabel('Data:')->setRequired(true)->addValidator('NotEmpty')->setAttrib(
            'href',
            '#gridModal'
        )->setAttrib('data-toggle', 'modal');

        $this->addElements([$id, $data]);
    }
}
