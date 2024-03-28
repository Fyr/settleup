<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Payments_Setup as Setup;

class Application_Model_Grid_Settlement_PaymentSetup extends Application_Model_Grid
{
    public function __construct()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $user = User::getCurrentUser();

        $paymentSetupEntity = new Setup();
        $paymentSetupHeader = [
            'header' => $paymentSetupEntity->getResource()->getInfoFieldsForPopup(),
            'sort' => ['payment_code' => 'ASC'],
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
            $paymentSetupEntity::class,
            $paymentSetupHeader,
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
