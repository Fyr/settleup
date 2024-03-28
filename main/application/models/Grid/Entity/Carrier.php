<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;

class Application_Model_Grid_Entity_Carrier extends Application_Model_Grid
{
    public function __construct()
    {
        $carrierEntity = new Application_Model_Entity_Entity_Carrier();

        $header = [
            'header' => $carrierEntity->getResource()->getInfoFields(),
            'sort' => ['name' => 'ASC'],
            'id' => static::class,
            'filter' => true,
            'checkboxField' => false,
            'callbacks' => [
                'action' => 'Application_Model_Grid_Callback_ActionCarriers',
                //                'tax_id' => 'Application_Model_Grid_Callback_Decrypt',
            ],
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
        ];

        if (User::getCurrentUser()->hasPermission(Permissions::CONTRACTOR_MANAGE)) {
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
            [],
            $button
        );
    }
}
