<?php

class Application_Model_Grid_Callback_DeductionBalance implements Application_Model_Grid_Callback_ExcelInterface
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        $negativeSign = '';
        $value = (float)$this->column;
        if ($value < 0) {
            $value = $value * -1;
            $negativeSign = '-';
        }
        if (isset($this->additionalParams['forReport'])) {
            $sign = '<span class="pull-left">' . $negativeSign . '$</span>';
        } else {
            $sign = $negativeSign . '$';
        }
        if ($value) {
            return $sign . number_format($value, 2);
        } else {
            return null;
        }
    }

    public function wrapper()
    {
        return 'class="num"';
    }

    public function getExcelValue($entity, $method, $processingModel = false)
    {
        return $entity->$method();
        //        if ($value == '$-') {
        //            $value = null;
        //        }
        //        return $value;
    }
}
