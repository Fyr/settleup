<?php

use Application_Model_Entity_System_FileStorageType as FileStorageType;

return [
    [
        'controller' => 'settlement_index',
        'id' => 'settlement_index',
        'label' => 'Settlement',
        'level' => 1,
    ],
    [
        'controller' => 'payments_index',
        'id' => 'payments_index',
        'label' => 'Compensation',
        'level' => 1,
        'pages' => [
            [
                'controller' => 'payments_payments',
                'id' => 'payments_payments',
                'label' => 'Compensation',
            ],
            [
                'controller' => 'file_index',
                'id' => 'payments_payments_upload',
                'action' => 'edit',
                'params' => ['file_type' => FileStorageType::CONST_PAYMENTS_FILE_TYPE],
                'label' => 'Upload Compensation',
            ],
            [
                'controller' => 'payments_setup',
                'id' => 'payments_setup',
                'label' => 'Master Compensation Templates',
            ],
            [
                'controller' => 'payments_setup',
                'id' => 'payments_setup_new',
                'action' => 'new',
                'label' => 'Create Master Compensation Template',
            ],
        ],
    ],
    [
        'controller' => 'deductions_index',
        'id' => 'deductions_index',
        'label' => 'Deductions',
        'level' => 1,
        'pages' => [
            [
                'controller' => 'deductions_deductions',
                'id' => 'deductions_deductions',
                'label' => 'Deductions',
            ],
            [
                'controller' => 'file_index',
                'id' => 'deductions_deductions_upload',
                'action' => 'edit',
                'params' => ['file_type' => FileStorageType::CONST_DEDUCTIONS_FILE_TYPE],
                'label' => 'Upload Deductions',
            ],
            [
                'controller' => 'deductions_setup',
                'id' => 'deductions_setup',
                'label' => 'Master Deduction Templates',
            ],
            [
                'controller' => 'deductions_setup',
                'id' => 'deductions_setup_new',
                'action' => 'new',
                'label' => 'Create Master Deduction Template',
            ],
        ],
    ],
    [
        'controller' => 'transactions_index',
        'id' => 'transactions_index',
        'label' => 'Reserves',
        'level' => 1,
        'pages' => [
            [
                'controller' => 'reserve_transactions',
                'id' => 'reserve_transactions',
                'label' => 'Reserve Transactions',
            ],
            [
                'controller' => 'reserve_accountcontractor',
                'label' => 'Power Unit Reserve Accounts',
            ],
            [
                'controller' => 'reserve_accountcontractor',
                'label' => 'Create Power Unit Reserve Account',
                'action' => 'new',
            ],
        ],
    ],
    [
        'controller' => 'transactions_disbursement',
        'id' => 'transactions_disbursement',
        'label' => 'Disbursements',
        'level' => 1,
    ],
    [
        'controller' => 'contractors_index',
        'id' => 'contractors_index',
        'label' => 'Contractors',
        'level' => 1,
    ],
    [
        'controller' => 'vendors_index',
        'id' => 'vendors_index',
        'label' => 'Vendors',
        'level' => 1,
    ],
    [
        'controller' => 'reporting_index',
        'id' => 'reporting_index',
        'label' => 'Reporting',
        'level' => 1,
    ],
    [
        'controller' => 'users_index',
        'id' => 'users_index',
        'label' => 'Users',
        'level' => 1,
    ],
];
