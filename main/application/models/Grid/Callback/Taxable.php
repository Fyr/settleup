<?php

use Application_Model_Entity_Payments_TaxableType as TaxableType;

class Application_Model_Grid_Callback_Taxable implements Application_Model_Grid_Callback_ExcelInterface
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        return $this->column ? TaxableType::VALUE_YES : TaxableType::VALUE_NO;
    }

    public function getExcelValue($entity, $method, $processingModel = false)
    {
        return $entity->$method();
    }
}
