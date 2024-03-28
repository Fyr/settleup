<?php

class Application_Model_Grid_Callback_ActionEscrowAccount
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        return '<a class="btn btn-primary" href="/carriers_index/escrow/id/' . $this->row['id'] . '"><i class="icon-pencil icon-white"></i> Edit</a> ';
    }

    public function wrapper()
    {
        return 'class="buttons"';
    }
}
