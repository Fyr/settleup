<?php

class Application_Model_Resource_Transactions_Disbursement extends Application_Model_Base_Resource
{
    protected $_name = 'disbursement_transaction';

    public function getInfoFields()
    {
        return [
            'entity_code' => 'ID',
            'name' => 'Recipient',
            'process_type_title' => 'Type',
            'disbursement_reference' => 'Disbursement Reference',
            'amount' => 'Amount',
        ];
    }
}
