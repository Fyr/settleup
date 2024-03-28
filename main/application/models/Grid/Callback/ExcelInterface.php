<?php

interface Application_Model_Grid_Callback_ExcelInterface
{
    /*
     * Return $value for excel export
     *
     * @var    Application_Model_Base_Entity      $entity
     * @var    string                             $method
     * @var    bool|Application_Model_Base_Entity $processingModel
     * @return                                    mixed
     */
    public function getExcelValue($entity, $method, $processingModel = false);
}
