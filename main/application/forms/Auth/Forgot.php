<?php

class Application_Form_Auth_Forgot extends Application_Form_Base
{
    public function init()
    {
        $this->setName('forgot');
        parent::init();

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email')->setRequired('true')->addValidator('EmailAddress')->addFilter('StripTags')->addFilter(
            'StringTrim'
        )->addFilter('StringToLower')->addValidator(
            'Db_RecordExists',
            false,
            [
                'table' => 'users',
                'field' => 'email',
                'messages' => 'That E-mail doesn\'t belong to any registered users in this system.',
            ]
        );

        $this->addElements([$email]);
        $this->setDefaultDecorators(['email']);
        $this->addSubmit('Send');
    }
}
