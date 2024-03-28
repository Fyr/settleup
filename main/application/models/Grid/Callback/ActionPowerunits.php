<?php

class Application_Model_Grid_Callback_ActionPowerunits
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        return '<a class="btn btn-primary" confirm-type="" href="/powerunits_index/edit/id/' . $this->row['id'] . '"><i class="icon-pencil icon-white"></i>&nbsp;Edit</a>';
    }

    public function wrapper()
    {
        return 'class="buttons"';
    }
}
