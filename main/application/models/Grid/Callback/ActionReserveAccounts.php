<?php

class Application_Model_Grid_Callback_ActionReserveAccounts
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        return '<a class="btn btn-primary" href="/reserve_accountpowerunit/edit/id/' . $this->row['id'] . '"><i class="icon-search icon-white"></i>&nbsp;View</a>';
    }

    public function wrapper()
    {
        return 'class="buttons"';
    }
}
