<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;

class Application_Model_Grid_Callback_ActionPaymentSetup
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        if (User::getCurrentUser()->hasPermission(Permissions::TEMPLATE_MANAGE)) {
            return '<a class="btn btn-primary" href="/payments_setup/edit/id/' . $this->row['id'] . '"><i class="icon-pencil icon-white"></i> Edit</a>' . ' <a class="btn btn-danger confirm" confirm-type="Deletion" delete-second-message="1" confirm-message="Deleting the master template will result in the deletion of all associated individual templates.<br/><br/> Do you still want to delete?" href="/payments_setup/delete/id/' . $this->row['id'] . '"><i class="icon-trash icon-white"></i> Delete</a>';
        } else {
            return '<a class="btn btn-primary" href="/payments_setup/edit/id/' . $this->row['id'] . '"><i class="icon-search icon-white"></i>&nbsp;View</a>';
        }
    }

    public function wrapper()
    {
        return 'class="buttons"';
    }
}
