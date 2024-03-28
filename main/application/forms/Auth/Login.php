<?php

class Application_Form_Auth_Login extends Application_Form_Base
{
    public function init()
    {
        $this->setName('login');
        parent::init();

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email')->setRequired('true')->addValidator('EmailAddress')->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->addFilter('StringToLower');

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password')->setRequired('true')->addFilter('StripTags')->addFilter('StringTrim');

        $this->addElements([$email, $password]);
        $this->setDefaultDecorators(['email', 'password']);
        $this->addSubmit('Log In');
    }
}
