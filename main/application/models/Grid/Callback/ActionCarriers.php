<?php

class Application_Model_Grid_Callback_ActionCarriers
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        if (!Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
            Application_Model_Entity_Entity_Permissions::CARRIER_MANAGE
        )) {
            $buttons = '<a class="btn btn-primary" href="/carriers_index/edit/id/' . $this->row['id'] . '"><i class="icon-search icon-white"></i> View</a>';
        } else {
            $buttons = '<a class="btn btn-primary" href="/carriers_index/edit/id/' . $this->row['id'] . '"><i class="icon-pencil icon-white"></i> Edit</a>' . ' <a class="btn btn-danger confirm" confirm-type="Deletion" href="/carriers_index/delete/id/' . $this->row['id'] . '"><i class="icon-trash icon-white"></i> Delete</a>';
        }

        return $buttons;
    }

    public function wrapper()
    {
        return 'class="buttons"';
    }
}
