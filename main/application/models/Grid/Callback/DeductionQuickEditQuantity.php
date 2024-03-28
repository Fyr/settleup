<?php

use Application_Model_Entity_Entity_Permissions as Permissions;

class Application_Model_Grid_Callback_DeductionQuickEditQuantity
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function wrapper()
    {
        if ($this->row['settlement_cycle_status'] < Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID) {
            $providerEntityTypeId = $this->row['provider_entity_type_id'];
            if ($providerEntityTypeId != Application_Model_Entity_Entity_Type::TYPE_VENDOR || ($providerEntityTypeId == Application_Model_Entity_Entity_Type::TYPE_VENDOR && Application_Model_Entity_Accounts_User::getCurrentUser(
            )->hasPermission(Permissions::VENDOR_DEDUCTION_MANAGE))) {
                $quickEdit = 'quick-edit';
            } else {
                $quickEdit = '';
            }

            return 'class="' . $quickEdit . ' num" field-name="quantity" field-type="num" record-id="' . $this->row['id'] . '"';
        }

        return 'class="num"';
    }
}
