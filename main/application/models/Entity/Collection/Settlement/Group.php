<?php

use Application_Model_Entity_Entity_Carrier as Division;
use Application_Model_Entity_Settlement_Group as Group;

class Application_Model_Entity_Collection_Settlement_Group extends Application_Model_Base_Collection
{
    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Group(),
            'division_id',
            new Division(),
            'id',
            ['division_name' => 'name', 'division_entity_id' => 'entity_id']
        );

        return $this;
    }

    public function addNonDeletedFilter($name = null)
    {
        $this->addFilter('deleted', 0);

        return $this;
    }
}
