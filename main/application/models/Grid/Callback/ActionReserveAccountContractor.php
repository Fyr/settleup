<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Grid_Callback_BaseTrait as BaseTrait;

class Application_Model_Grid_Callback_ActionReserveAccountContractor
{
    use BaseTrait;

    public function body()
    {
        $user = User::getCurrentUser();
        if ($user->isContractor() || $user->isVendor()) {
            return '<a class="btn btn-primary" href="/reserve_accountcontractor/edit/id/' . $this->row['id'] . '"><i class="icon-search icon-white"></i> View</a> ';
        }

        return '<a class="btn btn-primary" href="/reserve_accountcontractor/edit/id/' . $this->row['id'] . '"><i class="icon-pencil icon-white"></i> Edit</a> ';
    }

    public function wrapper()
    {
        return 'class="buttons"';
    }
}
