<?php

use Application_Model_Entity_System_FileStorageType as FileStorageType;

return [
    [
        'controller' => 'settlement_index',
        'label' => 'Settlement',
        'level' => 1,
    ],
    [
        'controller' => 'payments_index',
        'label' => 'Compensation',
        'level' => 1,
        'pages' => [
            [
                'controller' => 'payments_payments',
                'label' => 'Compensation',
            ],
            [
                'controller' => 'file_index',
                'action' => 'edit',
                'params' => ['file_type' => FileStorageType::CONST_PAYMENTS_FILE_TYPE],
                'label' => 'Upload Compensation',
            ],
            [
                'controller' => 'payments_setup',
                'label' => 'Master Compensation Templates',
            ],
            [
                'controller' => 'payments_setup',
                'action' => 'new',
                'label' => 'Create Master Compensation Template',
            ],
        ],
    ],
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
            ],
            [
                'controller' => 'deductions_setup',
                'label' => 'Master Deduction Templates',
            ],
            [
                'controller' => 'deductions_setup',
                'action' => 'new',
                'label' => 'Create Master Deduction Template',
            ],
        ],
    ],
    [
        'controller' => 'transactions_index',
        'label' => 'Reserves',
        'level' => 1,
        'pages' => [
            [
                'controller' => 'reserve_transactions',
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
                'label' => 'Settlement Info',
                'controller' => 'settlement_rule',
            ],
            [
                'label' => 'Escrow Accounts',
                'controller' => 'escrow',
            ],
            [
                'label' => 'Custom Fields',
                'controller' => 'custom',
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
        'controller' => 'powerunits_index',
        'label' => 'Power Units',
        'level' => 1,
        'pages' => [
            [
                'controller' => 'powerunits_index',
                'label' => 'Power Units',
            ],
            [
                'controller' => 'file_index',
                'action' => 'edit',
                'params' => ['file_type' => FileStorageType::CONST_POWERUNIT_FILE_TYPE],
                'label' => 'Upload Power Units',
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
        'label' => 'Reporting',
        'level' => 1,
    ],
    [
        'controller' => 'system_index',
        'label' => 'System',
        'level' => 1,
        'pages' => [
            [
                'label' => 'Users',
                'controller' => 'users_index',
            ],
            [
                'label' => 'Interest Allocation',
                'controller' => 'interestrate_index',
            ],
        ],
    ],
];
