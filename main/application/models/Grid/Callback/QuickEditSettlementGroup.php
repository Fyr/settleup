<?php

class Application_Model_Grid_Callback_QuickEditSettlementGroup
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function wrapper()
    {
        return 'class="quick-edit num nullable" field-name="settlement_group" field-type="num" record-id="' . $this->row['id'] . '"';
    }
}
