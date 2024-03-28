<?php

class Application_Model_Grid_Callback_Quantity implements Application_Model_Grid_Callback_ExcelInterface
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function wrapper()
    {
        return 'class="num"';
    }

    public function body()
    {
        return number_format($this->column, 1);
    }

    public function getExcelValue($entity, $method, $processingModel = false)
    {
        return number_format($entity->$method(), 1);
    }
}
