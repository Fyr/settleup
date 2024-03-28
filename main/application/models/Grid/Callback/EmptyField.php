<?php

class Application_Model_Grid_Callback_EmptyField
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        return '';
    }

    public function wrapper()
    {
        return 'class="empty"';
    }
}
