<?php

class Application_Model_Resource_Entity_Contact_Info extends Application_Model_Base_Resource
{
    protected $_name = 'entity_contact_info';

    /**
     * return all contacts by entity
     *
     * @return array
     */
    public function getContactsByEntity(Application_Model_Base_Entity $entity)
    {
        $select = $this->select()->setIntegrityCheck(false);//This locks the table to allow joins
        $select->joinLeft('organisation_types', 'organisation_types.id = organisation.organisation_type_id');
        $select->where('entity_id', $entity->getEntityId());

        return $this->fetchAll(
            'entity_id=' . $entity->getEntityId()
        )->toArray();
    }
}
