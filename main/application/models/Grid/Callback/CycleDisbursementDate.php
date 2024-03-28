<?php

class Application_Model_Grid_Callback_CycleDisbursementDate implements Application_Model_Grid_Callback_ExcelInterface
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        if (isset($this->row['source_id']) && isset($this->row['disbursement_date'])) {
            return $this->row['disbursement_date'];
        } else {
            return $this->column;
        }
    }

    public function getExcelValue($entity, $method, $processingModel = false)
    {
        if ($entity->getSourceId() && $entity->getDisbursementDate()) {
            return $entity->getDisbursementDate();
        } else {
            return $entity->$method();
        }
    }
}
