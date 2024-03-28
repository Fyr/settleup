<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Payments_Setup as Setup;

class Application_Model_Grid_Payment_Setup extends Application_Model_Grid
{
    public function __construct()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $user = User::getCurrentUser();

        $paymentSetupEntity = new Setup();
        $header = [
            'header' => $paymentSetupEntity->getResource()->getInfoFields(),
            'sort' => ['payment_code' => 'ASC'],
            'filter' => true,
            'id' => self::class,
            'callbacks' => [
                'billing_title' => Application_Model_Grid_Callback_Frequency::class,
                'quantity' => Application_Model_Grid_Callback_Num::class,
                'action' => Application_Model_Grid_Callback_ActionPaymentSetup::class,
                'rate' => Application_Model_Grid_Callback_Balance::class,
                'taxable' => Application_Model_Grid_Callback_Taxable::class,
            ],
            'buttons' => Application_Model_Grid_Header_PaymentSetups::class,
            'service' => [
                'header' => ['action' => 'Action'],
                'bindOn' => 'id',
            ],
        ];

        $customFilters = ['addCarrierFilter', 'addNonDeletedFilter'];

        $grid = parent::__construct(
            $paymentSetupEntity::class,
            $header,
            [],
            $customFilters,
            []
        );

        $entityId = (int)$request->getParam('entity', 0);

        if (!$entityId) {
            $grid->getControllerDataStorage();
            $entityId = (isset($grid->controllerStorage['entity']) && $request->getControllerName(
            ) !== 'payments_setup') ? $grid->controllerStorage['entity'] : 0;
        }

        if ($entityId) {
            $grid->getControllerDataStorage()->gridData[$grid::class]['entity'] = $entityId;
            $filter = ['addFilterByEntityId' => $entityId];
            $contractor = Contractor::staticLoad($entityId, 'entity_id');
            if ($contractor->getId()) {
                $user->setLastSelectedContractor($contractor->getId())->save();
            }
            $grid->setFilter($filter);
            $header['header'] = $paymentSetupEntity->getResource()->getInfoFieldsIndividual();
            $header['callbacks']['action'] = Application_Model_Grid_Callback_ActionPaymentSetupEdit::class;
            $header['checkboxField'] = false;
            $header['sortable'] = false;
            $header['buttons'] = Application_Model_Grid_Header_Empty::class;
            $grid->setHeader($header);
            $this->setTitle('Contractor Compensation Templates');
        } else {
            $this->setTitle('Master Compensation Templates');
            $grid->setCustomFilters($customFilters);
        }

        if ($request->getControllerName() == 'payments_setup' && !$request->getParam('entity', 0)) {
            $grid->setResetFilters(['addFilterByEntityId']);
        }

        return $grid;
    }
}
