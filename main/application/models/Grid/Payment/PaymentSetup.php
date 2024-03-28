<?php

use Application_Model_Entity_Payments_Setup as Setup;

class Application_Model_Grid_Payment_PaymentSetup extends Application_Model_Grid
{
    public function __construct()
    {
        $paymentSetupEntity = new Setup();
        $header = [
            'header' => $paymentSetupEntity->getResource()->getInfoFieldsForPopup(),
            'sort' => ['payment_code' => 'ASC'],
            'filter' => true,
            'id' => static::class,
            'pagination' => false,
            'callbacks' => [
                'billing_title' => 'Application_Model_Grid_Callback_Frequency',
                'quantity' => 'Application_Model_Grid_Callback_Num',
                'rate' => 'Application_Model_Grid_Callback_Balance',
            ],
            'ignoreMassactions' => true,
        ];
        $filters = ['addCarrierFilter', 'addNonDeletedFilter', 'addMasterFilter'];

        return parent::__construct(
            $paymentSetupEntity::class,
            $header,
            [],
            $filters
        );
    }
}
