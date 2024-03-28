<?php

class Application_Model_Grid_Callback_ActionReserveAccountHistory
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        if ($this->view->gridModel->getSettlementCycle()->getStatusId(
        ) == Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID) {
            return '<a class="btn btn-primary" href="/reserve_accountcontractor/view/id/' . $this->row['reserve_account_contractor_id'] . '?back=' . urlencode(
                'settlement_index/contractor/id/' . $this->row['contractor_id']
            ) . '"><i class="icon-search icon-white"></i> View</a>';
        } else {
            return '<a class="btn btn-primary" href="/reserve_accountcontractor/edit/id/' . $this->row['reserve_account_contractor_id'] . '?back=' . urlencode(
                'settlement_index/contractor/id/' . $this->row['contractor_id']
            ) . '"><i class="icon-pencil icon-white"></i> Edit</a>';
        }
    }

    public function wrapper()
    {
        return 'class="buttons"';
    }
}
