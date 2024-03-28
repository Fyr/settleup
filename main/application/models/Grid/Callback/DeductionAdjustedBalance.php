<?php


class Application_Model_Grid_Callback_DeductionAdjustedBalance
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function wrapper()
    {
        return 'class="num"';
    }

    public function body()
    {
        $negativeSign = '';
        $value = (float)$this->column;
        if (is_null($this->column)) {
            return "&#x2015;";
        } else {
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
    }
}
