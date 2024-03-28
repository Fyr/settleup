<?php

class Application_Model_Grid_Reporting_Contractor extends Application_Model_Grid
{
    public function __construct()
    {
        $contractorEntity = new Application_Model_Entity_Entity_Contractor();
        $contractorsHeader = [
            'header' => $contractorEntity->getResource()->getInfoFieldsForReportPopup(),
            'sort' => ['entity_id' => 'ASC'],
            'id' => static::class,
            'disabledSort' => [
                'tax_id' => true,
            ],
            'disabledFilter' => [
                'tax_id' => true,
            ],
            'checkboxField' => 'entity_id',
            'titleField' => $contractorEntity->getTitleColumn(),
            'filter' => true,
            'pagination' => false,
            'ignoreMassactions' => true,
            'callbacks' => [
                'tax_id' => 'Application_Model_Grid_Callback_Decrypt',
            ],
        ];

        return parent::__construct(
            $contractorEntity::class,
            $contractorsHeader,
            false,
            ['addFilterByCarrierContractor', 'addFilterByVendorVisibility'],
            null,
            [
                'defaultFilters' => [
                    [
                        'status',
                        Application_Model_Entity_System_ContractorStatus::STATUS_ACTIVE,
                        '=',
                        true,
                        Application_Model_Base_Collection::WHERE_TYPE_AND,
                    ],
                ],

            ]
        );
    }
}
