<?php

class Application_Form_Account_UserEntity extends Application_Form_Base
{
    public function init()
    {
        $this->setName('user_entity');
        parent::init();

        $id = new Application_Form_Element_Hidden('id');

        $userId = new Application_Form_Element_Hidden('user_id');

        $deleted = new Application_Form_Element_Hidden('deleted');
        $deleted->setValue(Application_Model_Entity_System_SystemValues::NOT_DELETED_STATUS);

        $entityId = new Application_Form_Element_Hidden('entity_id');

        $entityIdTitle = new Zend_Form_Element_Text('entity_id_title');
        $entityIdTitle->setLabel('Division')
            ->setRequired()
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $carrierId = new Application_Form_Element_Hidden('carrier_id');

        $carrierTitle = new Application_Form_Element_Hidden('carrier_name');

        $this->addElements([$entityId, $entityIdTitle, $carrierId, $carrierTitle, $deleted, $id, $userId]);
        $this->setDefaultDecorators([
            'entity_id_title',
            'carrier_name',
        ]);
    }

    public function configure()
    {
        if ($entityId = $this->getElement('entity_id')->getValue()) {
            if (!$this->getElement('entity_id_title')->getValue()) {
                $entity = Application_Model_Entity_Entity::staticLoad($entityId);
                $this->getElement('entity_id_title')->setValue($entity->getName());
            }
            if (!$this->getElement('carrier_name')->getValue()) {
                if ($carrierId = $this->getElement('carrier_id')->getValue()) {
                    $carrier = Application_Model_Entity_Entity_Carrier::staticLoad($carrierId);
                } else {
                    if (isset($entity)) {
                        $carrier = $entity->getEntityByType();
                    } else {
                        $carrier = Application_Model_Entity_Entity::staticLoad($entityId)->getEntityByType(
                        );
                    }
                }

                $this->getElement('carrier_name')->setValue($carrier->getName());
            }
        }
        parent::configure();
    }
}
