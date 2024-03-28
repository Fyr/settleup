<?php

use Application_Model_Entity_System_ContractorStatus as ContractorStatus;

class Application_Model_Grid_Reporting_ReserveAccountContractor extends Application_Model_Grid
{
    public function __construct()
    {
        $reserveAccountContractorEntity = new Application_Model_Entity_Accounts_Reserve_Contractor();

        if (!Application_Model_Entity_Accounts_User::getCurrentUser()->isContractor()) {
            $additionalFilters = [
                'raContractorStatus' => [
                    'options' => (new Application_Model_Resource_System_ContractorStatus())->getStatusFilterOptions(),
                    'defaultValue' => ContractorStatus::STATUS_ACTIVE,
                ],
            ];
            $filter = [
                'defaultFilters' => [
                    [
                        'contractor_status',
                        ContractorStatus::STATUS_ACTIVE,
                        '=',
                        true,
                        Application_Model_Base_Collection::WHERE_TYPE_AND,
                    ],
                ],
            ];
        } else {
            $additionalFilters = [];
            $filter = [];
        }

        $header = [
            'header' => $reserveAccountContractorEntity->getResource()->getInfoFieldsForReport(),
            'titleField' => 'account_name',
            'sort' => ['contractor_entity_id' => 'ASC', 'priority' => 'ASC'],
            'dragrows' => false,
            'id' => static::class,
            'filter' => true,
            'checkboxField' => 'reserve_account_id',
            'idField' => 'id',
            'priorityFilterField' => 'contractor_entity_id',
            'additionalFilters' => $additionalFilters,
            'pagination' => false,
            'ignoreMassactions' => true,
        ];

        $customFilters = [
            'addVisibilityFilterForUser',
            'addNonDeletedFilter',
        ];

        $grid = parent::__construct(
            $reserveAccountContractorEntity::class,
            $header,
            [],
            $customFilters,
            [],
            $filter
        );

        return $grid;
    }
}
