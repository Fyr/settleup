<?php

class Application_Model_Grid_Callback_MilesLE
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function wrapper()
    {
        return 'class="num"';
    }

    public function body()
    {
        return $this->row['loaded_miles'] + $this->row['empty_miles'];
    }
}
