<?php

use Application_Model_Entity_System_FileStorageType as FileStorageType;

class Application_Model_Grid_Header_ReserveAccountPowerunit implements Application_Model_Grid_Header_HeaderInterface
{
    public function getData($grid, $view)
    {
        $buttons = [
            'add' => [
                "caption" => "Create New",
                "button_class" => "btn-success",
                "icon_class" => "icon-plus",
                "url" => '/reserve_accountpowerunit/new',
            ],
            'upload' => [
                'caption' => 'Upload',
                'button_class' => 'btn-success',
                'icon_class' => 'icon-file',
                'url' => '/file_index/edit/file_type/' . FileStorageType::CONST_CONTRACTOR_RA_FILE_TYPE,
            ],
        ];

        return $buttons;
    }
}
