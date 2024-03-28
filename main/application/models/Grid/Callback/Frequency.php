<?php

class Application_Model_Grid_Callback_Frequency
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        if ($this->row['recurring']) {
            return $this->column;
        } else {
            return '';
        }
    }
}
