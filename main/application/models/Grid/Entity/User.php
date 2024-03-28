<?php

class Application_Model_Grid_Entity_User extends Application_Model_Grid
{
    protected $rewriteColumns = ['name' => 'LOWER(users.name)'];

    public function __construct()
    {
        $userEntity = new Application_Model_Entity_Accounts_User();

        $header = [
            'header' => [
                'id' => "ID",
                'name' => 'User Name',
                'email' => 'User Email',
                'role_title' => 'User Type',
                'entity_name' => 'Company',
            ],
            'id' => static::class,
            'callbacks' => [
                'action' => 'Application_Model_Grid_Callback_ActionUsers',
            ],
            'sort' => ['id' => 'ASC'],
            'filter' => true,
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
        ];

        $currentUser = $userEntity->getCurrentUser();

        if ($currentUser->isAdmin()) {
            $massaction = [
                "delete" => [
                    "caption" => "Delete Selected",
                    "button_class" => "btn-danger confirm-delete btn-multiaction",
                    "confirm-type" => "Deletion",
                    "icon_class" => "icon-trash",
                    "action-type" => "delete",
                    "url" => '/users_index/multiaction',
                ],
            ];
            $button = [
                'add' => [
                    "caption" => "Add User",
                    "button_class" => "btn-success",
                    "icon_class" => "icon-plus",
                    "url" => '/users_index/new',
                ],
            ];
        } else {
            if ($currentUser->isCarrier() && ($currentUser->hasPermission(
                Application_Model_Entity_Entity_Permissions::CONTRACTOR_USER_CREATE
            ) || $currentUser->hasPermission(
                Application_Model_Entity_Entity_Permissions::VENDOR_USER_CREATE
            ))) {
                $button = [
                    'add' => [
                        "caption" => "Add User",
                        "button_class" => "btn-success",
                        "icon_class" => "icon-plus",
                        "url" => '/users_index/new',
                    ],
                ];
            } else {
                $button = [];
            }
            $massaction = [];
        }

        $customFilters = ['addNonDeletedFilter'];

        if ($currentUser->isModerator()) {
            $customFilters[] = 'addModeratorFilter';
        }

        if ($userEntity->getCurrentUser()->isCarrier()) {
            $customFilters[] = 'addCarrierFilter';
        }

        parent::__construct(
            $userEntity::class,
            $header,
            $massaction,
            $customFilters,
            $button
        );
    }
}
