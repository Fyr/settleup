<?php

class Application_Model_Entity_Entity_Contact_Info extends Application_Model_Base_Entity
{
    public function save()
    {
        if ($this->getData('value') == null) {
            $this->delete();
        } else {
            parent::save();
        }

        return $this;
    }

    public function getType($id)
    {
        $entity = new Application_Model_Entity_Entity_Contact_Type();
        $entity->load($id);

        return $entity;
    }
}
