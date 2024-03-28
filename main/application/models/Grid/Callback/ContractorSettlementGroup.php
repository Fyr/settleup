<?php

class Application_Model_Grid_Callback_ContractorSettlementGroup
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body(): string
    {
        if (!$this->column) {
            return '-';
        }

        return $this->row['settlement_group'] ?: $this->column;
    }
}
