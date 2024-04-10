<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Entity_Permissions as Permissions;

class Application_Model_Grid_Entity_Contractor extends Application_Model_Grid
{
    protected $rewriteColumns = [
        'code' => 'contractor.code',
    ];

    public function __construct()
    {
        $contractorEntity = new Contractor();

        if (User::getCurrentUser()->isOnboarding()) {
            $actionCallback = 'Application_Model_Grid_Callback_ViewButton';
            $button = null;
            $additionalFilters = [];
        } else {
            $actionCallback = 'Application_Model_Grid_Callback_ActionContractors';
            $button = [];
            if (User::getCurrentUser()->hasPermission(Permissions::CONTRACTOR_MANAGE)) {
                $button['add'] = [
                    "caption" => "Create New",
                    "button_class" => "btn btn-success contractors-add-new",
                    "icon_class" => "icon-plus",
                    'url' => '/contractors_index/new',
                ];
            }

            if (User::getCurrentUser()->hasPermission(Permissions::UPLOADING)) {
                $button['upload'] = [
                    'caption' => 'Upload',
                    'button_class' => 'btn-success',
                    'icon_class' => 'icon-file',
                    'url' => '/file_index/edit/file_type/' . Application_Model_Entity_System_FileStorageType::CONST_CONTRACTOR_FILE_TYPE,
                ];
            }
            $additionalFilters = [
                'contractorStatus' => [
                    'options' => (new Application_Model_Resource_System_ContractorStatus())->getStatusFilterOptions(),
                    'grid' => $this,
                ],
            ];
        }

        $header = [
            'header' => $contractorEntity->getResource()->getInfoFieldsForListAction(),
            'sort' => ['company_name' => 'ASC'],
            'disabledSort' => [
                'tax_id' => true,
            ],
            'filter' => true,
            'disabledFilter' => [
                'tax_id' => true,
            ],
            'id' => static::class,
            'callbacks' => [
                'action' => $actionCallback,
                'tax_id' => 'Application_Model_Grid_Callback_Decrypt',
                'start_date' => 'Application_Model_Grid_Callback_DateFormat',
                'termination_date' => 'Application_Model_Grid_Callback_DateFormat',
                'rehire_date' => 'Application_Model_Grid_Callback_DateFormat',
                //'settlement_group' => Application_Model_Grid_Callback_QuickEditSettlementGroup::class,
            ],
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
            'additionalFilters' => $additionalFilters,
        ];

        if (User::getCurrentUser()->isOnboarding()) {
            $header['header'] = $contractorEntity->getResource()->getInfoFieldsForListActionVendor();
        }
        /*        if (!User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE)) {
                    unset($header['callbacks']['settlement_group']);
                }*/

        $customFilters = ['addCarrierFilter', 'vendorFilter', 'addSettlementGroup'];

        $grid = parent::__construct(
            $contractorEntity::class,
            $header,
            null,
            $customFilters,
            $button
        );

        $request = Zend_Controller_Front::getInstance()->getRequest();
        if ($request->getControllerName() == 'contractors_index') {
            $grid->setResetFilters(['addFilterByEntityId']);
        }

        return $grid;
    }
}
