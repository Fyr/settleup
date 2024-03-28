<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Deductions_Setup as Setup;
use Application_Model_Entity_Entity_Contractor as Contractor;

class Application_Model_Grid_Settlement_DeductionSetup extends Application_Model_Grid
{
    public function __construct()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $user = User::getCurrentUser();

        $deductionSetupEntity = new Setup();
        $deductionSetupHeader = [
            'header' => $deductionSetupEntity->getResource()->getInfoFieldsForPopup(),
            'sort' => ['deduction_code' => 'ASC'],
            'id' => static::class,
            'filter' => true,
            'checkboxField' => 'master_setup_id',
            'pagination' => false,
            'callbacks' => [
                'billing_title' => 'Application_Model_Grid_Callback_Frequency',
                'quantity' => 'Application_Model_Grid_Callback_Num',
                'rate' => 'Application_Model_Grid_Callback_Balance',
            ],
            'ignoreMassactions' => true,
        ];

        $filters = ['addCarrierFilter', 'addNonDeletedFilter'];

        $grid = parent::__construct(
            $deductionSetupEntity::class,
            $deductionSetupHeader,
            [],
            $filters
        );

        $entityId = (int)$request->getParam('id', 0);

        if (!$entityId) {
            $grid->getControllerDataStorage();
            $entityId = (isset($grid->controllerStorage['entity']) && $request->getControllerName(
            ) !== 'settlement_index') ? $grid->controllerStorage['entity'] : 0;
        }

        if ($entityId) {
            $grid->getControllerDataStorage()->gridData[$grid::class]['entity'] = $entityId;
            $filters = $grid->getFilter();
            $filters['addFilterByEntityId'] = $entityId;
            $contractor = Contractor::staticLoad($entityId, 'entity_id');
            if ($contractor->getId()) {
                $user->setLastSelectedContractor($contractor->getId())->save();
            }
            $grid->setFilter($filters);
        }

        if ($request->getControllerName() == 'settlement_index' && !$request->getParam('id', 0)) {
            $grid->setResetFilters(['addFilterByEntityId']);
        }

        return $grid;
    }
}
