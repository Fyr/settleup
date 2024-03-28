<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_System_ContractorStatus as ContractorStatus;
use Application_Model_Grid_Callback_ActionReserveAccountContractor as ActionCallback;
use Application_Model_Grid_Callback_Balance as BalanceCallback;
use Application_Model_Grid_Callback_DateFormat as DateFormatCallback;
use Application_Model_Grid_Callback_Priority as PriorityCallback;

class Application_Model_Grid_ReserveAccount_Contractor extends Application_Model_Grid
{
    public function __construct()
    {
        $reserveAccountContractorEntity = new Application_Model_Entity_Accounts_Reserve_Contractor();
        $user = User::getCurrentUser();

        $request = Zend_Controller_Front::getInstance()->getRequest();

        $additionalFilters = [
            'raContractorStatus' => [
                'options' => (new Application_Model_Resource_System_ContractorStatus())->getStatusFilterOptions(),
                'defaultValue' => ContractorStatus::STATUS_ACTIVE,
            ],
        ];

        $header = [
            'header' => $reserveAccountContractorEntity->getResource()->getInfoFields(),
            'sort' => ['contractor_entity_id' => 'ASC', 'priority' => 'ASC'],
            'dragrows' => true,
            'id' => static::class,
            'filter' => true,
            'checkboxField' => false,
            'idField' => 'id',
            'priorityFilterField' => 'contractor_entity_id',
            'callbacks' => [
                'created_at' => DateFormatCallback::class,
                'priority' => PriorityCallback::class,
                'min_balance' => BalanceCallback::class,
                'contribution_amount' => BalanceCallback::class,
                'current_balance' => BalanceCallback::class,
                'initial_balance' => BalanceCallback::class,
                'accumulated_interest' => BalanceCallback::class,
                'action' => ActionCallback::class,
            ],
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
            'additionalFilters' => $additionalFilters,
        ];

        if ($user->hasPermission(Permissions::CONTRACTOR_MANAGE) && !$user->isContractor() && !$user->isVendor()) {
            $header['buttons'] = 'Application_Model_Grid_Header_ReserveAccountContractor';
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
                    'contractor_status',
                    ContractorStatus::STATUS_ACTIVE,
                    '=',
                    true,
                    Application_Model_Base_Collection::WHERE_TYPE_AND,
                ],
            ],
        ];

        $entityId = (int)$request->getParam('entity', 0);

        if ($entityId) {
            if (isset($header['additionalFilters']['raContractorStatus'])) {
                unset($header['additionalFilters']['raContractorStatus']);
            }
            if (isset($filter['defaultFilters'])) {
                unset($filter['defaultFilters']);
            }
        }

        $grid = parent::__construct(
            $reserveAccountContractorEntity::class,
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

        if ($entityId) {
            $grid->getControllerDataStorage()->gridData[$grid::class]['entity'] = $entityId;
            $filter = $grid->getFilter();
            $filter['addFilterByEntityId'] = $entityId;
            $contractor = (new Application_Model_Entity_Entity_Contractor())->load($entityId, 'entity_id');
            if ($contractor->getId()) {
                $user->setLastSelectedContractor($contractor->getId())->save();
            }
            $grid->setFilter($filter);
        }

        if ($request->getControllerName() == 'reserve_accountcontractor' && !$request->getparam('entity', 0)) {
            $grid->setResetFilters(['addFilterByEntityId']);
        }

        return $grid;
    }

    public function setFilter($data)
    {
        if (isset($data['vendor_name'])) {
            $data['entity_2.name'] = $data['vendor_name'];
            unset($data['vendor_name']);
        }

        return parent::setFilter($data);
    }
}
