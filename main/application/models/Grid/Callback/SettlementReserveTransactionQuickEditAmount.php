<?php

use Application_Model_Entity_System_ReserveTransactionTypes as ReserveTransactionTypes;
use Application_Model_Entity_System_SettlementCycleStatus as SettlementCycleStatus;

class Application_Model_Grid_Callback_SettlementReserveTransactionQuickEditAmount
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function wrapper()
    {
        if ($this->row['settlement_cycle_status'] < SettlementCycleStatus::APPROVED_STATUS_ID) {
            if ($this->row['type'] >= ReserveTransactionTypes::ADJUSTMENT_DECREASE
                && $this->row['settlement_cycle_status'] >= SettlementCycleStatus::PROCESSED_STATUS_ID
            ) {
                return 'class="num"';
            } else {
                return 'class="quick-edit num" field-name="amount" record-id="' . $this->row['id'] . '"';
            }
        }

        return 'class="num"';
    }

    public function body()
    {
        $sign = ($this->row['type'] == ReserveTransactionTypes::CONTRIBUTION) ? '-' : '';

        return $sign.'$'.number_format($this->column, 2);
    }
}
