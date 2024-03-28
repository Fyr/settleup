<?php

class Application_Model_Grid_Callback_SettlementReserveTransactionBalance
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function wrapper()
    {
        return 'class="num"';
    }

    public function body()
    {
        return '$'.number_format($this->column, 2);
    }
}
