<?php

class Application_Model_Grid_Callback_Priority
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function wrapper()
    {
        return '';
    }

    public function body()
    {
        return $this->column + 1;
    }
}
