<?php

use Application_Model_Entity_System_ReserveTransactionTypes as ReserveTransactionTypes;
use Application_Model_Entity_System_SettlementCycleStatus as SettlementCycleStatus;

class Application_Model_Grid_Callback_ActionSettlementReserveTransaction
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        if ($this->row['settlement_cycle_status'] == SettlementCycleStatus::APPROVED_STATUS_ID || ($this->row['settlement_cycle_status'] == SettlementCycleStatus::PROCESSED_STATUS_ID && ($this->row['type'] == ReserveTransactionTypes::ADJUSTMENT_INCREASE || $this->row['type'] == ReserveTransactionTypes::ADJUSTMENT_DECREASE)) || !Application_Model_Entity_Accounts_User::getCurrentUser(
        )->hasPermission(Application_Model_Entity_Entity_Permissions::SETTLEMENT_DATA_MANAGE)) {
            return '<a class="btn btn-primary" href="/reserve_transactions/edit/id/' . $this->row['id'] . '"><i class="icon-search icon-white"></i>&nbsp;View</a>';
        } else {
            return '<a class="btn btn-primary" href="/reserve_transactions/edit/id/' . $this->row['id'] . '?back=' . urlencode(
                'settlement_index/contractor/id/' . $this->row['contractor_id']
            ) . '"><i class="icon-pencil icon-white"></i>&nbsp;Edit</a>' . ' <a class="btn btn-danger confirm" data-confirm-description-title="Deleting" confirm-type="Deletion" href="/reserve_transactions/delete/id/' . $this->row['id'] . '?back=' . urlencode(
                'settlement_index/contractor/id/' . $this->row['contractor_id']
            ) . '"><i class="icon-trash icon-white"></i>&nbsp;Delete</a>';
        }
    }

    public function wrapper()
    {
        return 'class="buttons"';
    }
}
