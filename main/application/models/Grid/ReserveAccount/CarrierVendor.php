<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_System_FileStorageType as FileStorageType;

class Application_Model_Grid_ReserveAccount_CarrierVendor extends Application_Model_Grid
{
    public function __construct()
    {
        $reserveAccountVendorEntity = new Application_Model_Entity_Accounts_Reserve_Vendor();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $user = User::getCurrentUser();

        if ($entityId = (int)$request->getParam('entity', 0)) {
            $entity = (new Application_Model_Entity_Entity())->load($entityId);

            if (($entity->isCarrier() && !$user->hasPermission(
                Permissions::RESERVE_ACCOUNT_CARRIER_VIEW
            )) || ($entity->isVendor() && !$user->hasPermission(Permissions::RESERVE_ACCOUNT_VENDOR_VIEW))) {
                Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->gotoSimple(
                    'index',
                    'settlement_index'
                );
            }

            $filter['addFilterByEntityId'] = $entityId;
            //            $filter = array(
            //                array(
            //                    'reserve_account.entity_id',
            //                    $entityId,
            //                    '=',
            //                    true,
            //                    $type = Application_Model_Base_Collection::WHERE_TYPE_AND
            //                )
            //            );
            $customFilters = ['addNonDeletedFilter'];

            if (($entity->isCarrier() && $user->hasPermission(
                Permissions::RESERVE_ACCOUNT_CARRIER_VIEW
            )) || ($entity->isVendor() && $user->hasPermission(Permissions::RESERVE_ACCOUNT_VENDOR_VIEW))) {
                $button = [
                    'add' => [
                        "caption" => "Create New",
                        "button_class" => "btn-success",
                        "icon_class" => "icon-plus",
                        "url" => '/reserve_accountcarriervendor/new/entity/' . $entityId,
                    ],
                ];
            } else {
                $button = [];
            }
        } else {
            $filter = null;
            $customFilters = [
                'addNonDeletedFilter',
                ['name' => 'addCarrierVendorFilter', 'value' => true],
            ];
            if ((!$user->hasPermission(Permissions::RESERVE_ACCOUNT_CARRIER_MANAGE) && !$user->hasPermission(
                Permissions::RESERVE_ACCOUNT_VENDOR_MANAGE
            )) || (!$user->hasPermission(Permissions::RESERVE_ACCOUNT_VENDOR_VIEW) && !$user->hasPermission(
                Permissions::RESERVE_ACCOUNT_CARRIER_MANAGE
            )) || (!$user->hasPermission(Permissions::RESERVE_ACCOUNT_CARRIER_VIEW) && !$user->hasPermission(
                Permissions::RESERVE_ACCOUNT_VENDOR_MANAGE
            )) || ($user->isVendor() && !$user->hasPermission(Permissions::RESERVE_ACCOUNT_VENDOR_MANAGE))) {
                $button = [];
            } else {
                $button = [
                    'add' => [
                        "caption" => "Create New",
                        "button_class" => "btn-success",
                        "icon_class" => "icon-plus",
                        "url" => '/reserve_accountcarriervendor/new',
                    ],
                    $button['upload'] = [
                        'caption' => 'Upload',
                        'button_class' => 'btn-success',
                        'icon_class' => 'icon-file',
                        'url' => '/file_index/edit/file_type/' . FileStorageType::CONST_VENDOR_RA_FILE_TYPE,
                    ],
                ];
            }
        }

        $header = [
            'header' => $reserveAccountVendorEntity->getResource()->getInfoFields(),
            'sort' => ['priority' => 'ASC'],
            'dragrows' => true,
            'id' => static::class,
            'filter' => true,
            'checkboxField' => false,
            'callbacks' => [
                'priority' => 'Application_Model_Grid_Callback_Priority',
                'min_balance' => 'Application_Model_Grid_Callback_Balance',
                'contribution_amount' => 'Application_Model_Grid_Callback_Balance',
                'current_balance' => 'Application_Model_Grid_Callback_Balance',
                'action' => 'Application_Model_Grid_Callback_ActionReserveAccountVendor',
            ],
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
        ];
        if ($entityId || $user->isVendor()) {
            $header['dragrows'] = false;
            $header['sortable'] = false;
        }

        $grid = parent::__construct(
            $reserveAccountVendorEntity::class,
            $header,
            [],
            $customFilters,
            $button,
            $filter
        );

        if ($request->getControllerName() == 'reserve_accountcarriervendor' && !$entityId) {
            $grid->setResetFilters(['addFilterByEntityId']);
        }

        return $grid;
    }
}
