<?php

class Application_Model_Grid_Transaction_Contractor extends Application_Model_Grid
{
    public function __construct()
    {
        $contractorsEntity = new Application_Model_Entity_Entity_Contractor();
        $contractorsHeader = [
            'header' => $contractorsEntity->getResource()->getInfoFieldsForPopup(),
            'sort' => ['company_name' => 'ASC'],
            'filter' => true,
            'disabledSort' => [
                'tax_id' => true,
            ],
            'disabledFilter' => [
                'tax_id' => true,
            ],
            'checkboxField' => 'entity_id',
            'pagination' => false,
            'ignoreMassactions' => true,
            'id' => static::class,
            'callbacks' => [
                'tax_id' => Application_Model_Grid_Callback_Decrypt::class,
            ],
        ];

        $customFilters = [
            'addFilterByCarrierContractor',
            'addFilterByVendorVisibility',
            'addNonDeletedFilter',
            'addConfiguredFilter',
        ];

        return parent::__construct(
            $contractorsEntity::class,
            $contractorsHeader,
            [],
            $customFilters,
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
