<?php

class Application_Model_Grid_Entity_User extends Application_Model_Grid
{
    protected $rewriteColumns = ['name' => 'LOWER(users.name)'];

    public function __construct()
    {
        $userEntity = new Application_Model_Entity_Accounts_User();
        $header = [
            'header' => $userEntity->getResource()->getInfoFields(),
            'id' => static::class,
            'callbacks' => [
                'action' => Application_Model_Grid_Callback_ActionUsers::class,
                'divisions' => Application_Model_Grid_Callback_UserDivisions::class,
            ],
            'sort' => ['id' => 'ASC'],
            'disabledSort' => [
                'divisions' => true,
            ],
            'filter' => true,
            'disabledFilter' => [
                'divisions' => true,
            ],
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
        ];

        $currentUser = $userEntity->getCurrentUser();
        $massaction = [];
        $button = [];
        if ($currentUser->isSuperAdmin()) {
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
        }

        $customFilters = [
            'addNonDeletedFilter',
            'addDivisionsInfo',
        ];

        if ($currentUser->isAdmin()) {
            $customFilters[] = 'addAdminFilter';
        }
        if ($currentUser->isManager()) {
            $customFilters[] = 'addManagerFilter';
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
