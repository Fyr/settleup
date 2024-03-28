<?php

use Application_Model_Entity_Powerunit_Powerunit as Powerunit;
use Application_Model_Entity_System_FileStorageType as FileStorageType;

class Application_Model_Grid_Powerunit_Powerunit extends Application_Model_Grid
{
    protected $rewriteColumns = [
        'id' => 'powerunit.id',
        'code' => 'powerunit.code',
    ];

    public function __construct()
    {
        $powerunit = new Powerunit();
        $actionCallback = 'Application_Model_Grid_Callback_ActionPowerunits';
        $button = [];
        $button['add'] = [
            "caption" => "Create New",
            "button_class" => "btn btn-success powerunits-add-new",
            "icon_class" => "icon-plus",
            'url' => '/powerunits_index/new',
        ];
        $button['upload'] = [
            'caption' => 'Upload',
            'button_class' => 'btn-success',
            'icon_class' => 'icon-file',
            'url' => '/file_index/edit/file_type/' . FileStorageType::CONST_POWERUNIT_FILE_TYPE,
        ];

        $header = [
            'header' => $powerunit->getResource()->getInfoFieldsForListAction(),
            'sort' => ['code' => 'ASC', 'contractor_id' => 'ASC'],
            'id' => static::class,
            'callbacks' => [
                'action' => $actionCallback,
                'domicile' => Application_Model_Grid_Callback_QuickEditDomicile::class,
            ],
            'filter' => true,
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
        ];

        $customFilters = [
            'addNonDeletedFilter',
            'addDivisionFilter',
        ];

        $grid = parent::__construct(
            $powerunit::class,
            $header,
            null,
            $customFilters,
            $button
        );

        return $grid;
    }

    public function getGridId()
    {
        return "Application_Model_Grid_Powerunit_Powerunit";
    }
}
