<?php

class Application_Model_Grid_User_Carrier extends Application_Model_Grid
{
    public function __construct()
    {
        $carrierEntity = new Application_Model_Entity_Entity_Carrier();

        $header = [
            'header' => $carrierEntity->getResource()->getInfoFieldsForPopup(),
            'sort' => ['entity_id' => 'ASC'],
            'titleField' => $carrierEntity->getTitleColumn(),
            'id' => static::class,
            'filter' => true,
            'pagination' => false,
            'ignoreMassactions' => true,
            'checkboxField' => false,
            'idField' => 'entity_id',
        ];

        return parent::__construct(
            $carrierEntity::class,
            $header,
            false,
            ['addNonDeletedFilter']
        );
    }
}
