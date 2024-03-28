<?php

class Application_Model_Grid_Callback_Action
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        if ($this->row['settlement_cycle_status'] != Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID) {
            return '<a class="btn btn-primary" href="/payments_payments/edit/id/' . $this->row['id'] . '"><i class="icon-pencil icon-white"></i>&nbsp;Edit</a>' . ' <a class="btn btn-danger confirm" confirm-type="Deletion" href="/payments_payments/delete/id/' . $this->row['id'] . '"><i class="icon-trash icon-white"></i>&nbsp;Delete</a>';
        } else {
            return '<a class="btn btn-primary" href="/payments_payments/edit/id/' . $this->row['id'] . '"><i class="icon-search icon-white"></i>&nbsp;View</a>';
        }
    }

    public function wrapper()
    {
        return 'class="buttons"';
    }
}
