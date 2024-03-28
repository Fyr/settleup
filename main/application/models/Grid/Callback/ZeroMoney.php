<?php

class Application_Model_Grid_Callback_ZeroMoney
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        return '$' . number_format(0, 2);
    }

    public function wrapper()
    {
        return 'class="num"';
    }
}
