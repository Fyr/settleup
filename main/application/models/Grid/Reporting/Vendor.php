<?php

class Application_Model_Grid_Reporting_Vendor extends Application_Model_Grid
{
    public function __construct()
    {
        $vendorEntity = new Application_Model_Entity_Entity_Vendor();
        $vendorsHeader = [
            'header' => $vendorEntity->getResource()->getInfoFieldsForPopup(),
            'sort' => ['entity_id' => 'ASC'],
            'checkboxField' => 'entity_id',
            'titleField' => $vendorEntity->getTitleColumn(),
            'filter' => true,
            'pagination' => false,
            'ignoreMassactions' => true,
            'id' => static::class,
        ];

        return parent::__construct(
            $vendorEntity::class,
            $vendorsHeader,
            false,
            ['addVisibilityFilterForUser', 'addNonDeletedFilter', 'addConfiguredFilter']
        );
    }
}
