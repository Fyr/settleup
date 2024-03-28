<?php

class Application_Model_Grid_Callback_ActionContractors
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        if (Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
            Application_Model_Entity_Entity_Permissions::CONTRACTOR_MANAGE
        )) {
            if ($this->row['status'] == Application_Model_Entity_System_ContractorStatus::STATUS_NOT_CONFIGURED) {
                return '<a class="btn btn-primary" confirm-type="" href="/contractors_index/edit/id/' . $this->row['id'] . '"><i class="icon-pencil icon-white"></i>&nbsp;Edit</a>';
            } elseif ($this->row['status'] != Application_Model_Entity_System_ContractorStatus::STATUS_TERMINATED) {
                return '<a class="btn btn-danger confirm" confirm-type="terminate" data-confirm-title="Confirm Termination" confirm-message="You are about to terminate contractor!" href="/contractors_index/changestatus/id/' . $this->row['id'] . '/status/STATUS_TERMINATED"><i class="icon-pause icon-white"></i>&nbsp;Terminate</a>&nbsp;' . '<a class="btn btn-primary" confirm-type="" href="/contractors_index/edit/id/' . $this->row['id'] . '"><i class="icon-pencil icon-white"></i>&nbsp;Edit</a>';
            } else {
                return '<a class="btn btn-success" href="/contractors_index/changestatus/id/' . $this->row['id'] . '/status/STATUS_ACTIVE"><i class="icon-refresh icon-white"></i>&nbsp;Contract</a>' . ' <a class="btn btn-primary" confirm-type="" href="/contractors_index/edit/id/' . $this->row['id'] . '"><i class="icon-pencil icon-white"></i>&nbsp;Edit</a>';
            }
        } else {
            return '<a class="btn btn-primary" confirm-type="" href="/contractors_index/edit/id/' . $this->row['id'] . '"><i class="icon-search icon-white"></i>&nbsp;View</a>';
        }
    }

    public function wrapper()
    {
        return 'class="buttons"';
    }
}
