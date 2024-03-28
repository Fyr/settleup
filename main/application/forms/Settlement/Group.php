<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Carrier as Carrier;
use Application_Model_Entity_Entity_Permissions as Permissions;

class Application_Form_Settlement_Group extends Application_Form_Base
{
    public $divisions = [];

    public function init()
    {
        parent::init();
        $this->divisions = (new Carrier())->getAllDivisions();
        $this->setName('settlement_group');

        $id = (new Zend_Form_Element_Text('id'))
            ->setLabel('ID')
            ->addValidator('Int', true, ['messages' => 'Entered value is invalid'])
            ->setAttrib(
                'readonly',
                'readonly'
            );

        $code = (new Zend_Form_Element_Text('code'))
            ->setLabel('Code')
            ->setRequired();

        $name = (new Zend_Form_Element_Text('name'))
            ->setLabel('Name')
            ->setRequired();

        $division = (new Zend_Form_Element_Select('division_id'))
            ->setLabel('Division')
            ->addmultiOptions(
                $this->getDivisionNames()
            );

        $this->addElements([
            $id,
            $code,
            $name,
            $division,
        ]);

        $this->setDefaultDecorators([
            'id',
            'code',
            'name',
            'division_id',
        ]);

        if (!User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_GROUP_MANAGE)) {
            foreach ($this->getElements() as $element) {
                $element->setAttrib('readonly', 'readonly');
            }
        } else {
            $this->addSubmit();
        }
    }

    public function getDivisionNames(): array
    {
        $data = [];
        foreach ($this->divisions as $id => $division) {
            $data[$id] = $division['name'];
        }

        return $data;
    }
}
