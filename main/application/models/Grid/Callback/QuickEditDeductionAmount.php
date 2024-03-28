<?php


class Application_Model_Grid_Callback_QuickEditDeductionAmount
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function wrapper()
    {
        return 'class="quick-edit num" field-name="deduction_amount" field-type="money" record-id="' . $this->row['id'] . '"';
    }

    public function body()
    {
        return '-$' . number_format($this->column, 2);
    }
}
