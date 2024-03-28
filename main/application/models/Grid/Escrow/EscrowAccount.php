<?php

class Application_Model_Grid_Escrow_EscrowAccount extends Application_Model_Grid
{
    public function __construct()
    {
        $escrowAccountEntity = new Application_Model_Entity_Accounts_Escrow();
        $header = [
            'header' => [
                'name' => 'Division',
                'escrow_account_holder' => 'Escrow Account Holder',
                'next_check_number' => 'Next Check Number',
            ],
            'sort' => ['name' => 'ASC'],
            'id' => static::class,
            'filter' => true,
            'checkboxField' => false,
            'callbacks' => [
                'action' => 'Application_Model_Grid_Callback_ActionEscrowAccount',
            ],
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
        ];

        return parent::__construct(
            $escrowAccountEntity::class,
            $header,
            [],
            ['addNonDeletedFilter']
        );
    }
}
