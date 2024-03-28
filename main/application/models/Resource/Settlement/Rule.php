<?php

class Application_Model_Resource_Settlement_Rule extends Application_Model_Base_Resource
{
    protected $_name = 'settlement_cycle_rule';

    public function getInfoFields()
    {
        return [
            'type' => 'Type',
            'cycle_start_date' => 'Start Date',
            'cycle_second_start_date' => 'Second Start Date',
            'payment_terms' => 'Processing Deadline',
            'disbursement_terms' => 'Disbursement Terms',
        ];
    }
}
