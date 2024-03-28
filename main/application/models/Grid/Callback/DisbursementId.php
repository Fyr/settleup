<?php

class Application_Model_Grid_Callback_DisbursementId
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        if ($this->row['tax_id']) {
            $value = $this->row['carrier_tax_id'];
        } elseif ($this->row['contractor_tax_id']) {
            $value = $this->row['contractor_tax_id'];
        } /*else {
            $value = $this->row['vendor_tax_id'];
        }*/

        return $value;
    }
}
