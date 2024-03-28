<?php

class Application_Model_Grid_Callback_QuickEditQuantity
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function wrapper()
    {
        if ($this->row['settlement_cycle_status'] < Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID) {
            return 'class="quick-edit num" field-name="quantity" field-type="num" record-id="' . $this->row['id'] . '"';
        }

        return 'class="num"';
    }
}
