<?php

class Application_Form_Auth_User extends Application_Form_Base
{
    public function init()
    {
        $this->setName('user');
        parent::init();

        $id = new Zend_Form_Element_Hidden('id');

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email : ')->setRequired('true')->addValidator('EmailAddress')->addFilter(
            'StripTags'
        )->addFilter('StringTrim')->addFilter('StringToLower')->addValidator(
            'Db_NoRecordExists',
            false,
            [
                'table' => 'users',
                'field' => 'email',
                'exclude' => [
                    'field' => 'id',
                    'value' => (int)Zend_Controller_Front::getInstance()->getRequest()->get('id'),
                ],
            ]
        )->addErrorMessage(
            'Sorry, it looks like "%value%"' . ' belongs to an existing account.'
        );
        ;
        ;

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Name : ')->setRequired('true')->addFilter('StripTags')->addFilter('StringTrim');

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password :')->setRequired('true')->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->addValidator(
            'Identical',
            true,
            ['token' => 'conf_password']
        );

        $confPassword = new Zend_Form_Element_Password('conf_password');
        $confPassword->setLabel('Re-enter password :')->setRequired('true')->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->addValidator(
            'Identical',
            true,
            ['token' => 'password']
        );

        $this->addElements([$id, $email, $name, $password, $confPassword]);
        $this->setDefaultDecorators(
            ['email', 'name', 'password', 'conf_password']
        );
        $this->addSubmit('Save');
    }
}
