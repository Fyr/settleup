<?php

use Application_Model_Entity_Settlement_Group as Group;
use Application_Model_Grid_Callback_ActionSettlementGroup as ActionSettlementGroup;
use Application_Model_Grid_Callback_DivisionName as DivisionName;
use Application_Model_Grid_Header_SettlementGroup as SettlementGroup;

class Application_Model_Grid_Settlement_Group extends Application_Model_Grid
{
    public function __construct()
    {
        $group = new Group();
        $header = [
            'header' => $group->getResource()->getInfoFields(),
            'id' => static::class,
            'filter' => true,
            'callbacks' => [
                'action' => ActionSettlementGroup::class,
                'division_name' => DivisionName::class,
            ],
            'checkboxField' => false,
            'buttons' => SettlementGroup::class,
            'sort' => ['id' => 'ASC'],
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
        ];

        return parent::__construct(
            $group::class,
            $header,
            [],
            ['addNonDeletedFilter']
        );
    }
}
