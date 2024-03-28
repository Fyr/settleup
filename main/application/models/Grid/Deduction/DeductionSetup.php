<?php

class Application_Model_Grid_Deduction_DeductionSetup extends Application_Model_Grid
{
    public function __construct()
    {
        $deductionSetupEntity = new Application_Model_Entity_Deductions_Setup();
        $header = [
            'header' => $deductionSetupEntity->getResource()->getInfoFieldsForPopup(),
            'sort' => ['provider_name' => 'ASC', 'deduction_code' => 'ASC'],
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

        return parent::__construct(
            $deductionSetupEntity::class,
            $header,
            [],
            [['name' => 'addUserVisibilityFilter', 'value' => [true, true]], 'addNonDeletedFilter', 'addMasterFilter']
        );
    }
}
