<?php

class Application_Form_Auth_Reset extends Application_Form_Base
{
    public function init()
    {
        $this->setName('reset');
        parent::init();

        $userHash = new Application_Form_Element_Hidden('user_hash');

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password')->setRequired('true')->addFilter('StripTags')->addFilter('StringTrim');

        $rPassword = new Zend_Form_Element_Password('r_password');
        $rPassword->setLabel('Repeat Password')->setRequired('true')->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->addValidator(
            'Identical',
            false,
            [
                'token' => 'password',
                'messages' => 'Passwords should be identical.',
            ]
        );

        $this->addElements([$userHash, $password, $rPassword]);
        $this->setDefaultDecorators(['password', 'r_password']);
        $this->addSubmit('Reset');
    }
}
