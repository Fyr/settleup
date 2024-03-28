<?php

use Application_Model_Entity_System_PaymentStatus as PaymentStatus;

class Application_Model_Grid_Callback_DisbursementStatus
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body()
    {
        if ($this->row['status'] == PaymentStatus::REVERSED_STATUS) {
            $value = 'Reversed';
        } else {
            $value = $this->row['cycle_disbursement_status_title'];
        }

        return $value;
    }

    public function getExcelValue($entity, $method, $processingModel = false)
    {
        if ($entity->getData('status') == PaymentStatus::REVERSED_STATUS) {
            return 'Reversed';
        } else {
            return $entity->getData('cycle_disbursement_status_title');
        }
    }
}
