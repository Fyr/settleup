<?php

class Application_Model_Grid_Callback_ReportPeriod implements Application_Model_Grid_Callback_ExcelInterface
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function wrapper()
    {
        return 'class="text"';
    }

    public function body()
    {
        return $this->row['cycle_start_date'] . ' - ' . $this->row['cycle_close_date'];
    }

    public function getExcelValue($entity, $method, $processingModel = false)
    {
        return $entity->getCycleStartDate() . ' - ' . $entity->getCycleCloseDate();
    }
}
