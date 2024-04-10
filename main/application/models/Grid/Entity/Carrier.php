<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;

class Application_Model_Grid_Entity_Carrier extends Application_Model_Grid
{
    public function __construct()
    {
        $carrierEntity = new Application_Model_Entity_Entity_Carrier();
        $currentUser = User::getCurrentUser();
        $header = [
            'header' => $carrierEntity->getResource()->getInfoFields(),
            'sort' => ['name' => 'ASC'],
            'id' => static::class,
            'filter' => true,
            'checkboxField' => false,
            'callbacks' => [
                'action' => Application_Model_Grid_Callback_ActionCarriers::class,
            ],
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
        ];

        $customFilters = [['name' => 'addVisibilityFilterForUser', 'value' => true]];

        if ($currentUser->hasPermission(Permissions::CARRIER_MANAGE)) {
            $button = [
                'add' => [
                    "caption" => "Create New",
                    "button_class" => "btn-success",
                    "icon_class" => "icon-plus",
                    "url" => '/carriers_index/new',
                ],
            ];
        } else {
            $button = [];
        }

        return parent::__construct(
            $carrierEntity::class,
            $header,
            [],
            $customFilters,
            $button
        );
    }
}
