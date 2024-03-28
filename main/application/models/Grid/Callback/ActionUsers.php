<?php

class Application_Model_Grid_Callback_ActionUsers
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        return '<a class="btn btn-primary" href="/users_index/edit/id/' . $this->row['id'] . '"><i class="icon-pencil icon-white"></i> Edit</a>' . ' <a class="btn btn-danger confirm" confirm-type="Deletion" href="/users_index/delete/id/' . $this->row['id'] . '"><i class="icon-trash icon-white"></i> Delete</a>';
    }

    public function wrapper()
    {
        return 'class="buttons"';
    }
}
