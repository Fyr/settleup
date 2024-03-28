<?php

use Application_Model_Entity_Settlement_Cycle as Cycle;

/**
 * @method $this staticLoad($id, $field = null)
 */
class Application_Model_Entity_Accounts_Reserve extends Application_Model_Base_Entity
{
    use Application_Model_Entity_PriorityTrait;

    /** @var Application_Model_Entity_Entity */
    protected $entity;

    public function _beforeSave()
    {
        parent::_beforeSave();

        $this->setMinBalance(str_replace(',', '', (string) $this->getMinBalance()));
        $this->setInitialBalance(str_replace(',', '', (string) $this->getInitialBalance()));
        $this->setCurrentBalance(str_replace(',', '', (string) $this->getCurrentBalance()));
        $this->setContributionAmount(str_replace(',', '', (string) $this->getContributionAmount()));

        if ($this->getEntityId() == null) {
            $entityModel = new Application_Model_Entity_Entity();
            $entityId = current($entityModel->getCollection()->getItems());

            $this->setEntityId($entityId->getId());
        }

        if (!is_numeric($this->getBalance())) {
            $this->setBalance($this->getCurrentBalance());
        }

        if (!is_numeric($this->getPriority())) {
            $this->setPriority(0);
        }

        if (!$this->getId()) {
            $this->setStartingBalance($this->getCurrentBalance());
            $this->setVerifyBalance($this->getCurrentBalance());
        }

        return $this;
    }

    /**
     * @return int return type of entity
     */
    public function getAccountType()
    {
        $entity = new Application_Model_Entity_Entity();
        $entity->load($this->getEntityId());

        return $entity->getEntityTypeId();
    }

    /**
     * @return Application_Model_Entity_Accounts_Reserve_Vendor|null
     */
    public function getVendorAccount()
    {
        $type = $this->getAccountType();
        if ($type == Application_Model_Entity_Entity_Type::TYPE_VENDOR || $type == Application_Model_Entity_Entity_Type::TYPE_CARRIER) {
            $vendorAccountEntity = new Application_Model_Entity_Accounts_Reserve_Vendor();
            $vendorAccountEntity->load($this->getId(), 'reserve_account_id');

            return $vendorAccountEntity;
        }

        return null;
    }

    public function getReserveAccountContractor($contractor)
    {
        $reserveAccountContractorEntity = new Application_Model_Entity_Accounts_Reserve_Contractor();

        $reserveAccountContractor = $reserveAccountContractorEntity->getCollection()->addFilter(
            'contractor_entity_id',
            $contractor
        )->addFilter('vendor_reserve_account_id', $this->getId())->addNonDeletedFilter()->setOrder(
            'priority',
            'ASC'
        )->getFirstItem();

        if ($reserveAccountContractor->getId()) {
            return $reserveAccountContractor;
        }

        return null;
    }

    /**
     * @return $this
     */
    public function deleteHistory()
    {
        $reserveAccountHistory = new Application_Model_Entity_Accounts_Reserve_History();
        $reserveAccountHistory->getResource()->delete(['reserve_account_id = ?' => $this->getId()]);

        return $this;
    }

    /**
     * @return Application_Model_Entity_Entity
     */
    public function getEntity()
    {
        if (!isset($this->entity) || $this->entity->getId() != $this->getEntityId()) {
            $this->entity = new Application_Model_Entity_Entity();
            $this->entity->load($this->getEntityId());
        }

        return $this->entity;
    }

    /**
     * get settlement next cycles and update balances on history
     *
     * @param $cycle
     * @return $this
     */
    public function updateSubsequentCycles($cycle)
    {
        $subsequentCycles = $cycle->getSubsequentCycles();

        foreach ($subsequentCycles as $subsequentCycle) {
            $this->updateHistory($subsequentCycle);
        }

        return $this;
    }

    public function updateHistory(Cycle $cycle)
    {
        $history = new Application_Model_Entity_Accounts_Reserve_History();
        $transaction = new Application_Model_Entity_Accounts_Reserve_Transaction();
        /** @var Application_Model_Resource_Accounts_Reserve_Transaction $transactionResource */
        $transactionResource = $transaction->getResource();
        $history->load([
            'reserve_account_id' => $this->getId(),
            'settlement_cycle_id' => $cycle->getId(),
        ]);

        if ($history->getId()) {
            $parentCycle = $cycle->getParentCycle();
            if ($parentCycle->getId()) {
                $previousHistory = new Application_Model_Entity_Accounts_Reserve_History();
                $previousHistory->load([
                    'reserve_account_id' => $this->getId(),
                    'settlement_cycle_id' => $parentCycle->getId(),
                ]);
                if ($previousHistory->getId()) {
                    $history->setVerifyBalance($previousHistory->getCurrentBalance());
                    $history->save();
                    $transactionResource->updateReserveAccountContractorStartingBalance(
                        $this->getId(),
                        $cycle->getId()
                    );
                    $transactionResource->updateReserveAccountContractorCurrentBalance($this->getId(), $cycle->getId());
                }
            }
        }
    }
}
