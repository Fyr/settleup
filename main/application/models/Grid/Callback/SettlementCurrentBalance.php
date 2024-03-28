<?php

class Application_Model_Grid_Callback_SettlementCurrentBalance
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        //        if (isset($this->view->gridModel)) {
        //            if ($this->view->gridModel->getSettlementCycleStatus() == Application_Model_Entity_System_SettlementCycleStatus::VERIFIED_STATUS_ID) {
        //                return '';
        //            }
        //        }

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
        return 'class="num"';
    }
}
