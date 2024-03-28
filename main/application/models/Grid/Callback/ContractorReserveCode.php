<?php

class Application_Model_Grid_Callback_ContractorReserveCode
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        return $this->row['vendor_reserve_code'] ?: $this->row['default_vendor_reserve_code'];
    }
}
