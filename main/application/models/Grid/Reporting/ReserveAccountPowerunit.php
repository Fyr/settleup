<?php

use Application_Model_Entity_System_PowerunitStatus as PowerunitStatus;

class Application_Model_Grid_Reporting_ReserveAccountPowerunit extends Application_Model_Grid
{
    public function __construct()
    {
        $reserveAccountPowerunitEntity = new Application_Model_Entity_Accounts_Reserve_Powerunit();

        if (!Application_Model_Entity_Accounts_User::getCurrentUser()->isSpecialist()) {
            // $additionalFilters = [
            //     'raPowerunitStatus' => [
            //         'options' => (new Application_Model_Entity_System_PowerunitStatus())->getStatusFilterOptions(),
            //         'defaultValue' => PowerunitStatus::STATUS_ACTIVE,
            //     ],
            // ];
            $filter = [
                'defaultFilters' => [
                    [
                        'powerunit_status',
                        PowerunitStatus::STATUS_ACTIVE,
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
            'header' => $reserveAccountPowerunitEntity->getResource()->getInfoFieldsForReport(),
            'titleField' => 'account_name',
            'sort' => ['priority' => 'ASC'],
            'dragrows' => false,
            'id' => static::class,
            'filter' => true,
            'checkboxField' => 'id',
            'idField' => 'id',
            // 'additionalFilters' => $additionalFilters,
            'pagination' => false,
            'ignoreMassactions' => true,
        ];

        $customFilters = [
            'addVisibilityFilterForUser',
            'addNonDeletedFilter',
        ];

        $grid = parent::__construct(
            $reserveAccountPowerunitEntity::class,
            $header,
            [],
            $customFilters,
            [],
            $filter
        );

        return $grid;
    }
}
