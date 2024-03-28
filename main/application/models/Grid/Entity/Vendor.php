<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_Entity_Vendor as Vendor;
use Application_Model_Entity_System_FileStorageType as FileStorageType;

class Application_Model_Grid_Entity_Vendor extends Application_Model_Grid
{
    public function __construct()
    {
        $vendorEntity = new Vendor();

        $header = [
            'header' => $vendorEntity->getResource()->getInfoFields(),
            'sort' => ['name' => 'ASC'],
            'checkboxField' => false,
            'id' => static::class,
            'callbacks' => [
                'action' => Application_Model_Grid_Callback_ActionVendor::class,
            ],
            'dragrows' => false,
            'filter' => true,
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
        ];

        $button = [];

        if (User::getCurrentUser()->hasPermission(Permissions::VENDOR_MANAGE)) {
            $button['add'] = [
                "caption" => "Create New",
                "button_class" => "btn-success",
                "icon_class" => "icon-plus",
                "url" => '/vendors_index/new',
            ];
            $button['upload'] = [
                'caption' => 'Upload',
                'button_class' => 'btn-success',
                'icon_class' => 'icon-file',
                'url' => '/file_index/edit/file_type/' . FileStorageType::CONST_VENDOR_FILE_TYPE,
            ];
        }
        $customFilters = [['name' => 'addVisibilityFilterForUser', 'value' => [true, true]]];

        return parent::__construct(
            $vendorEntity::class,
            $header,
            [],
            $customFilters,
            $button
        );
    }
}
