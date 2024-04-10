<?php

class Application_Model_Grid_Transaction_Powerunit extends Application_Model_Grid
{
    protected $rewriteColumns = ['code' => 'powerunit.code'];

    public function __construct()
    {
        $powerunitEntity = new Application_Model_Entity_Powerunit_Powerunit();
        $powerunitHeader = [
            'header' => $powerunitEntity->getResource()->getInfoFieldsShort(),
            'sort' => ['code' => 'ASC'],
            'disabledSort' => true,
            'filter' => true,
            'disabledFilter' => [
                'tax_id' => true,
            ],
            'checkboxField' => 'id',
            'pagination' => false,
            'ignoreMassactions' => true,
            'id' => static::class,
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
