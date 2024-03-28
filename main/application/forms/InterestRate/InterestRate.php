<?php


class Application_Form_InterestRate_InterestRate extends Application_Form_Base
{
    public function init()
    {
        $this->setName('Interest Rate');
        parent::init();

        $id = new Application_Form_Element_Hidden('id');

        $rate = (new Zend_Form_Element_Text('rate'))
            ->setLabel('Rate ')
            ->setRequired()
            ->addValidators([
                ['name' => 'Float', true, ['messages' => 'Invalid value, valid from 0 to 100 inclusive.',]],
                ['name' => 'Between', true, [
                    'min' => 0,
                    'max' => 100,
                    'messages' => 'Field value needs to be between 0 and 100.',
                ]],
            ])
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $this->addElements(
            [
                $id,
                $rate,
            ]
        );

        $this->setDefaultDecorators(
            [
                'id',
                'rate',
            ]
        );

        $this->addSubmit('Save');
    }
}
