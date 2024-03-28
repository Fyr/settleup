<?php

abstract class Application_Model_Entity_Entity_Base extends Application_Model_Base_Entity
{
    protected $_entityType;
    protected $entity;

    public function _beforeSave()
    {
        if ($this->getEntityId() == null) {
            $entityModel = $this->getEntity();
            $entityModel->setData(
                [
                    'user_id' => Application_Model_Entity_Accounts_User::getCurrentUser()->getId(),
                    'entity_type_id' => $this->_entityType,
                    'name' => $this->getName(),
                ]
            );
            $entityModel->save();

            $this->setEntityId($entityModel->getId());
        } else {
            if ($this->getOriginalData($this->getTitleColumn()) != $this->getName() || $this->isDataChanged()) {
                $this->getEntity()->setName($this->getName());
            }
            if ($this->getDeleted() !== null) {
                $this->getEntity()->setDeleted($this->getDeleted());
            }
            if ($this->getEntity()->isDataChanged()) {
                $this->getEntity()->save();
            }
        }

        if ($this->getData('id') === '') {
            $this->unsetData('id');
        }

        return parent::_beforeSave();
    }

    public function _afterDelete()
    {
        if ($entityId = $this->getEntityId()) {
            $entityModel = new Application_Model_Entity_Entity();
            $entityModel->load($entityId);
            $entityModel->delete();
        }

        parent::_afterDelete();
    }

    /**
     * returns current entity id based on the current user
     *
     * @static
     * @return int
     */
    public static function getCurrentEntityId()
    {
        return 1;
    }

    abstract public function getCurrentCarrier();

    abstract public function getCurrentContractor();

    /**
     * @return Application_Model_Base_Collection
     */
    public function getCollection()
    {
        $collection = parent::getCollection();

        return $collection->addFieldsForSelect(
            $this,
            'entity_id',
            new Application_Model_Entity_Entity(),
            'id',
            ['deleted']
        )->addNonDeletedFilter('entity.deleted');
    }

    /**
     * @return Application_Model_Entity_Entity
     */
    public function getEntity()
    {
        if (!isset($this->entity) || $this->entity->getId() != $this->getEntityId()) {
            $this->entity = new Application_Model_Entity_Entity();
            if ($this->getEntityId()) {
                $this->entity->load($this->getEntityId());
            }
        }

        return $this->entity;
    }
}
