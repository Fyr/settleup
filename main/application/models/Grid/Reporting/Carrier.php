<?php

class Application_Model_Grid_Reporting_Carrier extends Application_Model_Grid
{
    public function __construct()
    {
        $carrierEntity = new Application_Model_Entity_Entity_Carrier();
        $carriersHeader = [
            'header' => $carrierEntity->getResource()->getInfoFieldsForPopup(),
            'sort' => ['entity_id' => 'ASC'],
            'checkboxField' => 'entity_id',
            'titleField' => $carrierEntity->getTitleColumn(),
            'filter' => true,
            'pagination' => false,
            'ignoreMassactions' => true,
            'id' => static::class,
        ];

        return parent::__construct(
            $carrierEntity::class,
            $carriersHeader,
            false,
            ['addVisibilityFilterForUser', 'addNonDeletedFilter', 'addConfiguredFilter']
        );
    }
}
