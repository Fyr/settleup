<?php

class Application_Model_Grid_Callback_ActionDeductions
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        $providerEntityTypeId = $this->row['provider_entity_type_id'];

        $user = Application_Model_Entity_Accounts_User::getCurrentUser();

        if ((($providerEntityTypeId == Application_Model_Entity_Entity_Type::TYPE_VENDOR && $user->hasPermission(
            Application_Model_Entity_Entity_Permissions::VENDOR_DEDUCTION_MANAGE
        ) && $this->row['settlement_cycle_status'] < Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID) || !$user->isVendor(
        )) && $this->row['settlement_cycle_status'] != Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID && $user->hasPermission(
            Application_Model_Entity_Entity_Permissions::SETTLEMENT_DATA_MANAGE
        )) {
            return '<a class="btn btn-primary" href="/deductions_deductions/edit/id/' . $this->row['id'] . '"><i class="icon-pencil icon-white"></i>&nbsp;Edit</a>' . ' <a class="btn btn-danger confirm' . ($this->row['recurring'] ? ' recurring' : '') . '" confirm-type="Deletion" href="/deductions_deductions/delete/id/' . $this->row['id'] . '"><i class="icon-trash icon-white"></i>&nbsp;Delete</a>';
            ;
        } elseif (($providerEntityTypeId == Application_Model_Entity_Entity_Type::TYPE_VENDOR && $user->hasPermission(
            Application_Model_Entity_Entity_Permissions::VENDOR_DEDUCTION_VIEW
        )) || $providerEntityTypeId != Application_Model_Entity_Entity_Type::TYPE_VENDOR) {
            return '<a class="btn btn-primary" href="/deductions_deductions/edit/id/' . $this->row['id'] . '"><i class="icon-search icon-white"></i>&nbsp;View</a>';
        } else {
            return 'none';
        }
    }

    public function wrapper()
    {
        return 'class="buttons"';
    }
}
