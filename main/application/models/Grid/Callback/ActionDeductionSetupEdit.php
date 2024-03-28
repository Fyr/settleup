<?php

class Application_Model_Grid_Callback_ActionDeductionSetupEdit
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        return '<a class="btn btn-primary" href="/deductions_setup/edit/id/' . $this->row['id'] . '"><i class="icon-pencil icon-white"></i> Edit</a>';
    }

    public function wrapper()
    {
        return 'class="buttons"';
    }
}
