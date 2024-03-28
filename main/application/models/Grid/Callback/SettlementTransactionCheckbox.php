<?php

use Application_Model_Entity_System_ReserveTransactionTypes as ReserveTransactionTypes;
use Application_Model_Entity_System_SettlementCycleStatus as SettlementCycleStatus;

class Application_Model_Grid_Callback_SettlementTransactionCheckbox
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        if ($this->row['settlement_cycle_status'] == SettlementCycleStatus::PROCESSED_STATUS_ID && ($this->row['type'] == ReserveTransactionTypes::ADJUSTMENT_INCREASE || $this->row['type'] == ReserveTransactionTypes::ADJUSTMENT_DECREASE)) {
            return '';
        } else {
            $data = '<input type="checkbox" value="' . $this->column . '" class="checkbox idField">';

            return $data;
        }
    }
}
