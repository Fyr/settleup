<?php

use Application_Model_Entity_Settlement_Rule as Rule;

class Application_Model_Grid_Settlement_Rules extends Application_Model_Grid
{
    public function __construct()
    {
        $rule = new Rule();
        $header = [
            'header' => [
                'name' => 'Division',
                'period_title' => 'Settlement Cycle',
                'payment_terms' => 'Processing Deadline',
                'disbursement_terms' => 'Disbursement Terms',
                'cycle_close_date' => 'Last Closed Settlement',
            ],
            'id' => static::class,
            'filter' => true,
            'callbacks' => [
                'action' => 'Application_Model_Grid_Callback_ActionSettlementRule',
                'cycle_close_date' => 'Application_Model_Grid_Callback_SettlementDates',
            ],
            'checkboxField' => false,
            'buttons' => 'Application_Model_Grid_Header_Empty',
            'sort' => ['name' => 'ASC'],
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
        ];

        return parent::__construct(
            $rule::class,
            $header,
            [],
            ['addNonDeletedFilter']
        );
    }
}
