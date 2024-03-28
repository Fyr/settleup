<?php

class Application_Model_Grid_Callback_ActionSettlementDeductions
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        if ($this->row['settlement_cycle_status'] != Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID && Application_Model_Entity_Accounts_User::getCurrentUser(
        )->hasPermission(Application_Model_Entity_Entity_Permissions::SETTLEMENT_DATA_MANAGE)) {
            return '<a class="btn btn-primary" href="/deductions_deductions/edit/id/' . $this->row['id'] . '?back=' . urlencode(
                'settlement_index/contractor/id/' . $this->row['contractor_id']
            ) . '"><i class="icon-pencil icon-white"></i>&nbsp;Edit</a>' . ' <a class="btn btn-danger confirm' . ($this->row['recurring'] ? ' recurring' : '') . '" data-confirm-description-title="Deleting" confirm-type="Deletion" href="/deductions_deductions/delete/id/' . $this->row['id'] . '?back=' . urlencode(
                'settlement_index/contractor/id/' . $this->row['contractor_id']
            ) . '"><i class="icon-trash icon-white"></i>&nbsp;Delete</a>';
        } else {
            return '<a class="btn btn-primary" href="/deductions_deductions/edit/id/' . $this->row['id'] . '"><i class="icon-search icon-white"></i>&nbsp;View</a>';
        }
    }

    public function wrapper()
    {
        return 'class="buttons"';
    }
}
