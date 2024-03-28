<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;

class Application_Model_Grid_Callback_ActionSettlementGroup
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        if (User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_GROUP_MANAGE)) {
            $buttons = '<a class="btn btn-primary" href="/settlement_group/edit/id/' . $this->row['id']
                . '"><i class="icon-pencil icon-white"></i> Edit</a>'
                . ' <a class="btn btn-danger confirm" confirm-type="Deletion" href="/settlement_group/delete/id/'
                . $this->row['id'] . '"><i class="icon-trash icon-white"></i> Delete</a>';
        } else {
            $buttons = '<a class="btn btn-primary" href="/settlement_group/edit/id/' . $this->row['id']
                . '"><i class="icon-search icon-white"></i> View</a>';
        }

        return $buttons;
    }

    public function wrapper()
    {
        return 'class="buttons"';
    }
}
