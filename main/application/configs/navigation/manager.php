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
                'controller' => 'reserve_accountpowerunit',
                'label' => 'Power Unit Reserve Accounts',
            ],
            [
                'controller' => 'reserve_accountpowerunit',
                'label' => 'Create Power Unit Reserve Account',
                'action' => 'new',
            ],
        ],
    ],
    [
        'controller' => 'carriers_index',
        'label' => 'Divisions',
        'level' => 1,
        'pages' => [
            [
                'label' => 'Divisions',
                'controller' => 'carriers_index',
            ],
            [
                'label' => 'Settlement Group',
                'controller' => 'settlement_group',
            ],
        ],
    ],
    [
        'controller' => 'contractors_index',
        'label' => 'Contractors',
        'level' => 1,
        'pages' => [
            [
                'controller' => 'contractors_index',
                'label' => 'Contractors',
            ],
            [
                'controller' => 'file_index',
                'action' => 'edit',
                'params' => ['file_type' => FileStorageType::CONST_CONTRACTOR_FILE_TYPE],
                'label' => 'Upload Contractors',
            ],
        ],
    ],
    [
        'controller' => 'vendors_index',
        'label' => 'Vendors',
        'level' => 1,
        'pages' => [
            [
                'controller' => 'vendors_index',
                'label' => 'Vendors',
            ],
            [
                'controller' => 'file_index',
                'action' => 'edit',
                'params' => ['file_type' => FileStorageType::CONST_VENDOR_FILE_TYPE],
                'label' => 'Upload Vendors',
            ],
        ],
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
