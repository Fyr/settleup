<?php

/**
 * @method $this staticLoad($id, $field = null)
 */
class Application_Model_Entity_Entity_ContractorVendor extends Application_Model_Base_Entity
{
    protected $_contractorId;
    public $isNewEntity;
    public $status;

    /**
     * @return array|null
     */
    public function getContractorData()
    {
        $contractorEntity = new Application_Model_Entity_Entity_Contractor();
        $contractorId = $this->getData('contractor_id');
        $contractorEntity->load($contractorId, 'entity_id');
        $contractorData = $contractorEntity->getData();
        unset($contractorData['id']);

        return $contractorData;
    }

    /**
     * Adds
     *
     * @param $contractorsId An array of ids of contractors that have been added
     * from the pop-up grid
     * @return Application_Model_Entity_Entity_CarrierContractor
     */
    public function addContractors($contractorsId)
    {
        foreach ($contractorsId as $contractorId) {
            $this->setData('start_date');
            $this->_contractorId = $contractorId;
            $this->setStatus();
            $this->changeStatus(
                Application_Model_Entity_System_ContractorStatus::STATUS_NOT_CONFIGURED
            );
        }

        return $this;
    }

    /**
     * @param string $status New status
     * @return Application_Model_Entity_Entity_CarrierContractor
     */
    public function changeStatus(
        $status = Application_Model_Entity_System_ContractorStatus::STATUS_ACTIVE
    ) {
        $this->status = $status;
        $this->save();

        return $this;
    }

    /**
     * @return Application_Model_Entity_Entity_ContractorVendor
     */
    protected function _beforeSave()
    {
        return $this;
    }

    protected function _beforeDelete()
    {
        $individualTemplates = (new Application_Model_Entity_Deductions_Setup())->getCollection()->addNonDeletedFilter(
        )->addFilterByEntityId($this->getContractorId())->addFilter('provider_id', $this->getVendorId())->getItems(
        );
        foreach ($individualTemplates as $indTemplate) {
            $indTemplate->setDeleted(Application_Model_Entity_System_SystemValues::DELETED_STATUS)->save();
            if ($indTemplate->getRecurring() == Application_Model_Entity_System_RecurringTitle::RECURRING_YES) {
                $deductions = (new Application_Model_Entity_Deductions_Deduction())->getCollection(
                )->addNonDeletedFilter()->addFilter('setup_id', $indTemplate->getId())->addFilter(
                    'settlement_cycle_status',
                    [
                    Application_Model_Entity_System_SettlementCycleStatus::VERIFIED_STATUS_ID,
                    Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID,
                    ],
                    'IN'
                )->getItems();
                foreach ($deductions as $deduction) {
                    $deduction->setRecurringParentId('')->save();
                }
            }
        }
    }
}
