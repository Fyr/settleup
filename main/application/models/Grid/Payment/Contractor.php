<?php

class Application_Model_Grid_Payment_Contractor extends Application_Model_Grid
{
    public function __construct()
    {
        $contractorsEntity = new Application_Model_Entity_Entity_Contractor();
        $contractorsHeader = [
            'header' => $contractorsEntity->getResource()->getInfoFieldsForPopup(),
            'sort' => ['company_name' => 'ASC'],
            'disabledSort' => [
                'tax_id' => true,
            ],
            'filter' => true,
            'disabledFilter' => [
                'tax_id' => true,
            ],
            'checkboxField' => 'entity_id',
            'pagination' => false,
            'ignoreMassactions' => true,
            'id' => static::class,
            'callbacks' => [
                'tax_id' => 'Application_Model_Grid_Callback_Decrypt',
            ],
        ];

        return parent::__construct(
            $contractorsEntity::class,
            $contractorsHeader,
            [],
            ['addFilterByCarrierContractor', 'addConfiguredFilter'],
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
