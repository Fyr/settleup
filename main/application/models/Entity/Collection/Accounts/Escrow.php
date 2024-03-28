<?php

class Application_Model_Entity_Collection_Accounts_Escrow extends Application_Model_Base_Collection
{
    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Application_Model_Entity_Accounts_Escrow(),
            'carrier_id',
            new Application_Model_Entity_Entity(),
            'id',
            ['name', 'deleted']
        );

        return $this;
    }

    public function getDeletedFieldName()
    {
        return 'entity.deleted';
    }
}
