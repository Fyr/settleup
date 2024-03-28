<?php

class Application_Model_Grid_Callback_ViewButton
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        return '<a class="btn btn-primary" href="/contractors_index/edit/id/' . $this->row['id'] . '"><i class="icon-search icon-white"></i>&nbsp;View</a>';
    }

    public function wrapper()
    {
        return 'class="action-buttons-wrapper"';
    }
}
