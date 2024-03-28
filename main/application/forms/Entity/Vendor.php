<?php

class Application_Form_Entity_Vendor extends Application_Form_Base
{
    use Application_Form_ContactSubformTrait;

    public function init()
    {
        $this->setName('vendor');
        parent::init();

        $id = new Application_Form_Element_Hidden('id');

        $entityId = new Application_Form_Element_Hidden('entity_id');

        $code = new Zend_Form_Element_Text('code');
        $code->setLabel('Code')->setRequired(true)->addFilter('StripTags')->addFilter('StringTrim')->addValidator(
            (new Application_Model_Validate_VendorCode(
                [
                    'table' => 'vendor',
                    'field' => 'code',
                ]
            ))
        );
        //            ->addValidator(
        //                'Db_NoRecordExists',
        //                false,
        //                array(
        //                    'table' => 'vendor',
        //                    'field' => 'code',
        //                    'exclude' => array(
        //                        'field' => 'id',
        //                        'value' => (int)Zend_Controller_Front::getInstance()
        //                                ->getRequest()->get('id')
        //                    ),
        //                    'messages' => 'Sorry, it looks like "%value%" belongs to an existing ID.'
        //                )
        //            );

        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Name')->setRequired(true)->addFilter('StripTags')->addFilter('StringTrim');

        $this->addElements(
            [
                $id,
                $entityId,
                $code,
                $name,
            ]
        );

        $this->setDefaultDecorators(
            [
                'code',
                'name',
            ]
        );

        $this->addSubmit('Save');
    }

    public function setupForEditAction()
    {
        $this->name->setAttrib('readonly', 'readonly');
        $this->code->setAttrib('readonly', 'readonly');
    }
}
