<?php

use Application_Model_Entity_System_FileStorageType as FileStorageType;

return [
    [
        'controller' => 'deductions_index',
        'label' => 'Deductions',
        'level' => 1,
        'pages' => [
            [
                'controller' => 'deductions_deductions',
                'label' => 'Deductions',
            ],
            [
                'controller' => 'file_index',
                'action' => 'edit',
                'params' => ['file_type' => FileStorageType::CONST_DEDUCTIONS_FILE_TYPE],
                'label' => 'Upload Deductions',
                'id' => 'file_index',
            ],
            [
                'controller' => 'deductions_setup',
                'label' => 'Master Deduction Templates',
                'id' => 'deductions_setup',
            ],
            [
                'controller' => 'deductions_setup',
                'action' => 'new',
                'label' => 'Create Master Deduction Template',
                'id' => 'deductions_setup_new',
            ],

        ],
    ],
    [
        'controller' => 'transactions_index',
        'label' => 'Reserves',
        'level' => 1,
        'id' => 'transactions_index',
        'pages' => [
            [
                'controller' => 'reserve_transactions',
                'label' => 'Reserve Transactions',
                'id' => 'reserve_transactions',
            ],
            [
                'controller' => 'reserve_accountpowerunit',
                'label' => 'Power Unit Reserve Account',
                'id' => 'reserve_accountpowerunit',
            ],
        ],
    ],
    [
        'controller' => 'contractors_index',
        'label' => 'Contractors',
        'level' => 1,
    ],
    [
        'controller' => 'reporting_index',
        'label' => 'Reporting',
        'level' => 1,
        'id' => 'reporting_index',
    ],
];
