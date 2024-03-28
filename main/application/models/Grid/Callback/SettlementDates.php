<?php

class Application_Model_Grid_Callback_SettlementDates
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        if ($this->row['cycle_start_date'] && $this->row['cycle_close_date']) {
            $startDate = DateTime::createFromFormat('Y-m-d', $this->row['cycle_start_date'])->format('n/j/Y');
            $closeDate = DateTime::createFromFormat('Y-m-d', $this->row['cycle_close_date'])->format('n/j/Y');

            return $startDate . ' - ' . $closeDate;
        }

        return '';
    }
}
