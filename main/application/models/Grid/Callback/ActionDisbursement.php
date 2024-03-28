<?php

class Application_Model_Grid_Callback_ActionDisbursement
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        if (Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
            Application_Model_Entity_Entity_Permissions::DISBURSEMENT_VIEW
        )) {
            if ($this->row['disbursement_status'] == Application_Model_Entity_System_PaymentStatus::APPROVED_STATUS || !Application_Model_Entity_Accounts_User::getCurrentUser(
            )->hasPermission(Application_Model_Entity_Entity_Permissions::DISBURSEMENT_MANAGE)) {
                return '<a class="btn btn-primary" href="/transactions_disbursement/edit/id/' . $this->row['id'] . '"><i class="icon-search icon-white"></i>&nbsp;View</a>';
            } elseif (Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
                Application_Model_Entity_Entity_Permissions::DISBURSEMENT_MANAGE
            )) {
                return '<a class="btn btn-primary" href="/transactions_disbursement/edit/id/' . $this->row['id'] . '"><i class="icon-pencil icon-white"></i>&nbsp;Edit</a>';
            }
        } else {
            return 'none';
        }
    }

    public function wrapper()
    {
        return 'class="buttons"';
    }
}
