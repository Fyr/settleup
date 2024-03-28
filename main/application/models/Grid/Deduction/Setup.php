<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Grid_Callback_ActionDeductionSetupEdit as ActionCallback;
use Application_Model_Grid_Callback_Balance as BalanceCallback;
use Application_Model_Grid_Callback_Frequency as FrequencyCallback;
use Application_Model_Grid_Callback_Num as NumCallback;
use Application_Model_Grid_Header_DeductionSetups as Buttons;

class Application_Model_Grid_Deduction_Setup extends Application_Model_Grid
{
    public function __construct()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $user = User::getCurrentUser();

        $deductionSetupEntity = new Application_Model_Entity_Deductions_Setup();
        $header = [
            'header' => $deductionSetupEntity->getResource()->getInfoFields(),
            'id' => self::class,
            'callbacks' => [
                'billing_title' => FrequencyCallback::class,
                'quantity' => NumCallback::class,
                'rate' => BalanceCallback::class,
                'action' => ActionCallback::class,
            ],
            'idField' => 'id',
            'buttons' => Buttons::class,
            'sort' => ['contractor_id' => 'ASC'],
            'dragrows' => true,
            'filter' => true,
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
        ];

        if ($user->isVendor()) {
            $header['dragrows'] = false;
        }

        $customFilters = ['addUserVisibilityFilter', 'addNonDeletedFilter'];

        $grid = parent::__construct(
            $deductionSetupEntity::class,
            $header,
            [],
            $customFilters,
            []
        );

        $entityId = (int) $request->getParam('entity', 0);

        if (!$entityId) {
            $grid->getControllerDataStorage();
            $entityId = (isset($grid->controllerStorage['entity'])
                    && $request->getControllerName() !== 'deductions_setup')
                ? $grid->controllerStorage['entity']
                : 0;
        }

        if ($entityId) {
            $grid->getControllerDataStorage()->gridData[$grid::class]['entity'] = $entityId;
            $filter = ['addFilterByEntityId' => $entityId];
            $contractor = (new Contractor())->load($entityId, 'entity_id');
            if ($contractor->getId()) {
                $user->setLastSelectedContractor($contractor->getId())->save();
            }
            $grid->setFilter($filter);
            $header['header'] = $deductionSetupEntity->getResource()->getInfoFieldsIndividual();
            $header['callbacks']['action'] = ActionCallback::class;
            $header['filter'] = false;
            $header['checkboxField'] = false;
            $header['buttons'] = Application_Model_Grid_Header_Empty::class;
            $grid->setHeader($header);
            $this->setTitle('Contractor Deduction Templates');
        } else {
            $this->setTitle('Master Deduction Templates');
            $grid->setCustomFilters($customFilters);
        }

        if ($request->getControllerName() == 'deductions_setup' && !$request->getParam('entity', 0)) {
            $grid->setResetFilters(['addFilterByEntityId']);
        }

        return $grid;
    }

    // public function setPriority($beforeList, $resultList, $page)
    // {
    //     $entity = new Application_Model_Entity_Deductions_Setup();
    //     $changed = false;
    //     foreach ($beforeList[0] as $priority => $id) {
    //         $resultId = $resultList[0][$priority];
    //         if ($id !== $resultId) {
    //             $entity->updatePriority($resultId, $priority);
    //             $changed = true;
    //         }
    //     }
    //     if ($changed) {
    //         $entity->load($resultId);
    //         if ($entity->getContractorId()) {
    //             $entity->getContractor()->setDeductionPriority(Contractor::PRIORITY_CUSTOM)->save();
    //         }
    //         $entity->reorderPriority();
    //     }
    //     return $this;
    // }
}
