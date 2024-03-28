<?php

use Application_Model_Entity_System_SettlementCycleStatus as CycleStatus;

class Application_Model_Grid_Callback_QuickEditTransactionAmount
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function wrapper()
    {
        if ($this->row['settlement_cycle_status'] < CycleStatus::APPROVED_STATUS_ID) {
            return 'class="quick-edit num" field-name="amount" field-type="money" record-id="' . $this->row['id'] . '"';
        }

        return 'class="num"';
    }

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
}
