<?php

class Application_Model_Grid_Callback_DeductionReportBalance implements Application_Model_Grid_Callback_ExcelInterface
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        $negativeSign = '';
        if (is_null($this->row['adjusted_balance'])) {
            $value = (float)$this->column;
        } else {
            $value = (float)$this->row['adjusted_balance'];
        }
        if ($value < 0) {
            $value = $value * -1;
            $negativeSign = '-';
        }
        if (isset($this->additionalParams['forReport'])) {
            $sign = '<span class="pull-left">' . $negativeSign . '$</span>';
        } else {
            $sign = $negativeSign . '$';
        }
        if ($value || !is_null($this->row['adjusted_balance'])) {
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
        if (is_null($entity->getAdjustedBalance())) {
            $value = $entity->getBalance();
        } else {
            $value = $entity->getAdjustedBalance();
        }

        return $value;
    }
}
