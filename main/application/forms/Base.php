<?php

use Application_Model_Base_CryptAdvanced as Crypt;
use Application_Model_Entity_Accounts_User as User;

abstract class Application_Form_Base extends Zend_Form
{
    protected $encryptedFields = [];
    protected $useCarrierKey = false;
    public $readonly = false;
    protected $data = [];

    public function init()
    {
        parent::init();

        $this->addElementPrefixPath(
            'Application_Model_Validate',
            APPLICATION_PATH . '/models/Validate/',
            'validate'
        );

        $this->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
        $this->setMethod(Zend_Form::METHOD_POST);

        $request = Zend_Controller_Front::getInstance()->getRequest();
        //        $url = new Zend_View_Helper_Url();
        //        $this->setAction($url->url(array(), 'default'));

        $this->setAction($request->getRequestUri());

        $this->setDecorators(
            [
                'FormElements',
                [
                    'HtmlTag',
                    ['tag' => 'fieldset'],
                ],
                [
                    'Form',
                    ['tag' => 'form', 'class' => 'form-horizontal'],
                ],
            ]
        );
    }

    public function setDefaultDecorators($elements = null)
    {
        $fieldDecorator = [
            'ViewHelper',
            [
                'Errors',
                ['tag' => 'span', 'class' => 'help-inline error'],
            ],
            [
                'Description',
                ['tag' => 'p', 'class' => 'help-block'],
            ],
            [
                ['data' => 'HtmlTag'],
                ['tag' => 'div', 'class' => 'controls'],
            ],
            [
                'Label',
                ['class' => 'control-label', 'requiredSuffix' => ' *'],
            ],
            [
                ['row' => 'HtmlTag'],
                ['tag' => 'div', 'class' => 'control-group'],
            ],
        ];

        $this->setElementDecorators($fieldDecorator, $elements);
    }

    public function addSubmit($label = 'Save')
    {
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel($label)->setAttrib('class', 'btn btn-primary')->setDecorators(
            [
                    'ViewHelper',
                    'Errors',
                    [
                        ['row' => 'HtmlTag'],
                        ['tag' => 'div', 'class' => 'form-actions'],
                    ],
                ]
        );

        $this->addElement($submit);
    }

    public function setupForEditAction()
    {
    }

    public function setupForNewAction()
    {
    }

    public function configure()
    {
        if ($this->getElement('id')->getValue()) {
            $this->setupForEditAction();
        } else {
            $this->setupForNewAction();
        }

        return $this;
    }

    public function readonly()
    {
        foreach ($this->getElements() as $element) {
            $element->setAttrib('readonly', 'readonly');
        }
        $this->removeElement('submit');
        $this->readonly = true;
    }

    public function changeDateFormat($date, $fromStringToDb = false)
    {
        if ($fromStringToDb) {
            return DateTime::createFromFormat('m/d/Y', $date)->format('Y-m-d');
        } else {
            return DateTime::createFromFormat('Y-m-d', $date)->format('m/d/Y');
        }
    }

    public function populate(array $data)
    {
        $this->data = $data;
        $data = $this->decryptData($data);

        return parent::populate($data);
    }

    public function getValues($param = false)
    {
        $data = parent::getValues($param);
        $data = $this->encryptData($data);

        return $data;
    }

    protected function decryptData($data)
    {
        if ($this->encryptedFields) {
            $crypt = new Crypt();
            $carrierId = $this->useCarrierKey ? $this->getCarrierKey() : false;
            $carrierKey = User::getCurrentUser()->getCarrierKey($carrierId);
            foreach ($this->encryptedFields as $field) {
                if (isset($data[$field]) && strlen((string) $data[$field])) {
                    $data[$field] = $crypt->decrypt($data[$field], $carrierKey);
                }
            }
        }

        return $data;
    }

    protected function encryptData($data)
    {
        if ($this->encryptedFields) {
            $crypt = new Crypt();
            $carrierId = $this->useCarrierKey ? $this->getCarrierKey() : false;
            $carrierKey = User::getCurrentUser()->getCarrierKey($carrierId);
            foreach ($this->encryptedFields as $field) {
                if (isset($data[$field]) && strlen((string) $data[$field])) {
                    $data[$field] = $crypt->encrypt($data[$field], $carrierKey);
                }
            }
        }

        return $data;
    }

    protected function getCarrierKey()
    {
        return false;
    }

    public function setEncryptedFields(array $fields = [])
    {
        $this->encryptedFields = $fields;

        return $this;
    }
}
