<?php

class Application_Model_Grid_Callback_ActionReserveAccountVendor
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        $raEntityTypeId = $this->row['entity_type_id'];
        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        if (($raEntityTypeId == Application_Model_Entity_Entity_Type::TYPE_DIVISION && $user->hasPermission(
            Application_Model_Entity_Entity_Permissions::RESERVE_ACCOUNT_CARRIER_MANAGE
        )) || ($raEntityTypeId == Application_Model_Entity_Entity_Type::TYPE_VENDOR && $user->hasPermission(
            Application_Model_Entity_Entity_Permissions::RESERVE_ACCOUNT_VENDOR_MANAGE
        ))) {
            return '<a class="btn btn-primary" href="/reserve_accountcarriervendor/edit/id/' . $this->row['id'] . '"><i class="icon-pencil icon-white"></i> Edit</a> ';
        } elseif (($raEntityTypeId == Application_Model_Entity_Entity_Type::TYPE_DIVISION && $user->hasPermission(
            Application_Model_Entity_Entity_Permissions::RESERVE_ACCOUNT_CARRIER_VIEW
        )) || ($raEntityTypeId == Application_Model_Entity_Entity_Type::TYPE_VENDOR && $user->hasPermission(
            Application_Model_Entity_Entity_Permissions::RESERVE_ACCOUNT_VENDOR_VIEW
        ))) {
            return '<a class="btn btn-primary" href="/reserve_accountcarriervendor/edit/id/' . $this->row['id'] . '"><i class="icon-search icon-white"></i> View</a> ';
        } else {
            return 'none';
        }
    }

    public function wrapper()
    {
        return 'class="buttons"';
    }
}
