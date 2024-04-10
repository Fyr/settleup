<?php

use Application_Model_Entity_Accounts_User as User;

class Application_Model_Entity_Entity_CarrierContractor extends Application_Model_Base_Entity
{
    protected $_titleColumn = 'company_name';
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
     * An array of ids of contractors that have been added
     *
     * @param $contractorsId
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
        if (!$this->getId() && !$this->getStartDate(
        ) && $status == Application_Model_Entity_System_ContractorStatus::STATUS_ACTIVE) {
            $status = Application_Model_Entity_System_ContractorStatus::STATUS_NOT_CONFIGURED;
        }
        $this->status = $status;
        $this->save();

        return $this;
    }

    /**
     * @return Application_Model_Entity_Entity_CarrierContractor
     */
    protected function _beforeSave()
    {
        if (!$this->getId() && !$this->getData('start_date')) {
            //            !$this->getData('start_date')
            //            && $this->getStatus()
            //            != Application_Model_Entity_System_ContractorStatus::
            //            STATUS_NOT_CONFIGURED
            //        ) {
            $this->setData(
                [
                    'carrier_id' => User::getCurrentUser()->getEntity()->getEntityId(),
                    'contractor_id' => $this->_contractorId,
                ]
            );
        }
        parent::_beforeSave();

        return $this;
    }
}
