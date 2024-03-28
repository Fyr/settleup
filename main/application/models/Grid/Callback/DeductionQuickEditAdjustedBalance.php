<?php

use Application_Model_Entity_Entity_Permissions as Permissions;

class Application_Model_Grid_Callback_DeductionQuickEditAdjustedBalance
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function wrapper()
    {
        if ($this->row['settlement_cycle_status'] == Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID) {
            $providerEntityTypeId = $this->row['provider_entity_type_id'];
            if ($providerEntityTypeId != Application_Model_Entity_Entity_Type::TYPE_VENDOR || ($providerEntityTypeId == Application_Model_Entity_Entity_Type::TYPE_VENDOR && Application_Model_Entity_Accounts_User::getCurrentUser(
            )->hasPermission(Permissions::VENDOR_DEDUCTION_MANAGE))) {
                $quickEdit = 'quick-edit';
            } else {
                $quickEdit = '';
            }

            return 'class="' . $quickEdit . ' num nullable" field-type="money" field-name="adjusted_balance" max-value="' . $this->row['amount'] . '" record-id="' . $this->row['id'] . '"';
        }

        return 'class="num"';
    }

    public function body()
    {
        $negativeSign = '';
        $value = (float)$this->column;
        if (is_null($this->column)) {
            return "&#x2015;";
        } else {
            if ($value < 0) {
                $value = $value * -1;
                $negativeSign = '-';
            }
            if (isset($this->additionalParams['forReport'])) {
                $sign = '<span class="pull-left">' . $negativeSign . '$</span>';
            } else {
                $sign = $negativeSign . '$';
            }

            return $sign . number_format($value, 2);
        }
    }
}
