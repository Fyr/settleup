<?php

class Application_Model_Grid_Callback_RecurringCheckbox
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        if ($this->row['recurring']) {
            $data = '<input type="checkbox" value="' . $this->column . '" class="checkbox idField recurring">';
        } else {
            $data = '<input type="checkbox" value="' . $this->column . '" class="checkbox idField">';
        }

        return $data;
    }
}
