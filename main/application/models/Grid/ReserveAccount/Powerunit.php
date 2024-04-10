<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_System_PowerunitStatus as PowerunitStatus;
use Application_Model_Grid_Callback_ActionReserveAccountPowerunit as ActionCallback;
use Application_Model_Grid_Callback_Balance as BalanceCallback;
use Application_Model_Grid_Callback_DateFormat as DateFormatCallback;
use Application_Model_Grid_Callback_Priority as PriorityCallback;

class Application_Model_Grid_ReserveAccount_Powerunit extends Application_Model_Grid
{
    public function __construct()
    {
        $reserveAccountPowerunitEntity = new Application_Model_Entity_Accounts_Reserve_Powerunit();
        $user = User::getCurrentUser();

        $request = Zend_Controller_Front::getInstance()->getRequest();

        $additionalFilters = [
            'raPowerunitStatus' => [
                'options' => (new PowerunitStatus())->getStatusFilterOptions(),
                'defaultValue' => PowerunitStatus::STATUS_ACTIVE,
            ],
        ];

        $header = [
            'header' => $reserveAccountPowerunitEntity->getResource()->getInfoFields(),
            // 'sort' => ['priority' => 'ASC'],
            'dragrows' => true,
            'id' => static::class,
            'filter' => true,
            'checkboxField' => false,
            'idField' => 'id',
            // 'priorityFilterField' => 'contractor_entity_id',
            'callbacks' => [
                'created_at' => DateFormatCallback::class,
                'priority' => PriorityCallback::class,
                'min_balance' => BalanceCallback::class,
                'contribution_amount' => BalanceCallback::class,
                'current_balance' => BalanceCallback::class,
                'initial_balance' => BalanceCallback::class,
                'accumulated_interest' => BalanceCallback::class,
                'action' => ActionCallback::class,
                'account_type' => 'Application_Model_Grid_Callback_ReserveAccountType',
            ],
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
            // 'additionalFilters' => $additionalFilters,
        ];

        if ($user->hasPermission(Permissions::CONTRACTOR_MANAGE) && !$user->isSpecialist() && !$user->isOnboarding()) {
            $header['buttons'] = 'Application_Model_Grid_Header_ReserveAccountPowerunit';
        } else {
            $header['buttons'] = 'Application_Model_Grid_Header_Empty';
            $header['dragrows'] = false;
        }

        $customFilters = [
            'addVisibilityFilterForUser',
            'addNonDeletedFilter',
        ];

        $filter = [
            'defaultFilters' => [
                [
                    'powerunit_status',
                    PowerunitStatus::STATUS_ACTIVE,
                    '=',
                    true,
                    Application_Model_Base_Collection::WHERE_TYPE_AND,
                ],
            ],
        ];

        $entityId = (int)$request->getParam('entity', 0);

        if ($entityId) {
            // if (isset($header['additionalFilters']['raPowerunitStatus'])) {
            //     unset($header['additionalFilters']['raPowerunitStatus']);
            // }
            if (isset($filter['defaultFilters'])) {
                unset($filter['defaultFilters']);
            }
        }

        $grid = parent::__construct(
            $reserveAccountPowerunitEntity::class,
            $header,
            [],
            $customFilters,
            null,
            $filter
        );

        if (!$entityId) {
            $grid->getControllerDataStorage();
            $entityId = $grid->controllerStorage['entity'] ?? 0;
        }

        // if ($entityId) {
        //     $grid->getControllerDataStorage()->gridData[$grid::class]['entity'] = $entityId;
        //     $filter = $grid->getFilter();
        //     $filter['addFilterByEntityId'] = $entityId;
        //     $powerunit = (new Application_Model_Entity_Powerunit_Powerunit())->load($entityId, 'entity_id');
        //     if ($powerunit->getId()) {
        //         $user->setLastSelectedPowerunit($powerunit->getId())->save();
        //     }
        //     $grid->setFilter($filter);
        // }

        if ($request->getControllerName() == 'reserve_accountpowerunit' && !$request->getparam('entity', 0)) {
            $grid->setResetFilters(['addFilterByEntityId']);
        }

        return $grid;
    }

    // public function setFilter($data)
    // {
    //     if (isset($data['vendor_name'])) {
    //         $data['entity_2.name'] = $data['vendor_name'];
    //         unset($data['vendor_name']);
    //     }

    //     return parent::setFilter($data);
    // }
}
