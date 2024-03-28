<?php

class Application_Model_Grid_Callback_ActionVendor
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        if (!Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
            Application_Model_Entity_Entity_Permissions::VENDOR_MANAGE
        )) {
            return '<a class="btn btn-primary" href="/vendors_index/edit/id/' . $this->row['id'] . '"><i class="icon-search icon-white"></i>&nbsp;View</a>';
        } else {
            return '<a class="btn btn-primary" href="/vendors_index/edit/id/' . $this->row['id'] . '"><i class="icon-pencil icon-white"></i> Edit</a>';
        }
    }

    public function wrapper()
    {
        return 'class="buttons"';
    }
}
