<?php

class Application_Model_Grid_Deduction_Powerunit extends Application_Model_Grid
{
    protected $rewriteColumns = ['code' => 'powerunit.code'];

    public function __construct()
    {
        $powerunitEntity = new Application_Model_Entity_Powerunit_Powerunit();
        $powerunitHeader = [
            'header' => $powerunitEntity->getResource()->getInfoFieldsForDeductionPopup(),
            'sort' => ['code' => 'ASC'],
            'disabledSort' => [
                'tax_id' => true,
            ],
            'filter' => true,
            'disabledFilter' => [
                'tax_id' => true,
            ],
            'checkboxField' => 'id',
            'pagination' => false,
            'ignoreMassactions' => true,
            'id' => static::class,
            'callbacks' => [
                'checkbox' => Application_Model_Grid_Callback_DeductionPowerunitCheckbox::class,
                'tax_id' => Application_Model_Grid_Callback_Decrypt::class,
            ],
        ];

        $customFilters = [
            'addFilterByCarrierContractor',
            ['name' => 'addFilterByVendorVisibility', 'value' => false],
            'addNonDeletedFilter',
            'addConfiguredFilter',
            'addSettlementGroupFilter',
        ];

        return parent::__construct(
            $powerunitEntity::class,
            $powerunitHeader,
            [],
            $customFilters,
            null,
            [
                'defaultFilters' => [
                    [
                        'status',
                        Application_Model_Entity_System_PowerunitStatus::STATUS_ACTIVE,
                        '=',
                        true,
                        Application_Model_Base_Collection::WHERE_TYPE_AND,
                    ],
                    [
                        'contractor.status',
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
