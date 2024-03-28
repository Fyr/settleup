<?php

use Application_Model_Entity_Deductions_Deduction as Deduction;
use Application_Model_Entity_Payments_Payment as Payment;

trait Application_Model_Recurring_UpdateNextRecurringTrait
{
    public function updateNextRecurrings()
    {
        $newTemplateData = $this->getData();
        unset($newTemplateData['id']);
        unset($newTemplateData['deleted']);
        /** @var Payment|Deduction $recurring */
        foreach ($this->getNextRecurrings() as $recurring) {
            $recurring->addData($newTemplateData)->updateNextRecurring();
        }
    }

    /**
     * @return Application_Model_Base_Collection|array
     */
    protected function getNextRecurrings()
    {
        /** @var Payment|Deduction $associatedEntity */
        $associatedEntity = $this->getAssociatedEntity();
        if ($this->getContractorId()) {
            $setupIds = [$this->getId()];
        } else {
            $setupIds = $this->getCollection()->addFilter('master_setup_id', $this->getId())->addFilter(
                'changed',
                0
            )->getField('id');
        }
        if ($setupIds) {
            $collection = $associatedEntity->getCollection()->addFilter('setup_id', $setupIds, 'IN')->addFilter(
                'settlement_cycle_id',
                null,
                'IS NULL',
                false
            )->addNonDeletedFilter($associatedEntity->getResource()->getTableName() . '.deleted');

            return $collection;
        }

        return [];
    }
}
