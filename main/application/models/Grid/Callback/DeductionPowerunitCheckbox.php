<?php

class Application_Model_Grid_Callback_DeductionPowerunitCheckbox
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        $dataContractor = '';
        if ($this->row['contractor_id']) {
            $dataContractor = ' data-contractor-id="' . $this->row['contractor_id'] . '"';
        }

        return '<input type="checkbox" value="' . $this->column . '"' . $dataContractor . ' class="checkbox idField">';
    }
}
