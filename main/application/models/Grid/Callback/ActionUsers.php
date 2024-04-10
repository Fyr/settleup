<?php

use Application_Model_Entity_Accounts_User as User;

class Application_Model_Grid_Callback_ActionUsers
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        $result = '<a class="btn btn-primary" href="/users_index/edit/id/' . $this->row['id']
            . '"><i class="icon-pencil icon-white"></i> Edit</a>';
        if (User::getCurrentUser()->isSuperAdmin()) {
            $result .= ' <a class="btn btn-danger confirm" confirm-type="Deletion" href="/users_index/delete/id/' .
                $this->row['id'] . '"><i class="icon-trash icon-white"></i> Delete</a>';
        }

        return $result;
    }

    public function wrapper()
    {
        return 'class="buttons"';
    }
}
