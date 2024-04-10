<?php

use Application_Model_Entity_Accounts_User as User;

$entityId = User::getCurrentUser()->getEntityId();

return [
    [
        'controller' => 'contractors_index',
        'action' => 'edit',
        'label' => 'Information',
        'level' => 1,
    ],
    [
        'controller' => 'reserve_accountpowerunit',
        'action' => 'list',
        'label' => 'Reserves',
        'level' => 1,
        'params' => ['entity' => $entityId],
    ],
    [
        'controller' => 'reporting_index',
        'label' => 'Reporting',
        'level' => 1,
    ],
];
