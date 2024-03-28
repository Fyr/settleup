<?php

class Application_Model_Grid_Callback_Amount
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

        return $sign . number_format($value, 2);
    }

    public function wrapper()
    {
        return 'class="num" amount-field="true"';
    }
}
