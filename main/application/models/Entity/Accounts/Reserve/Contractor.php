<?php

use Application_Model_Entity_Entity_Contractor as Contractor;

class Application_Model_Entity_Accounts_Reserve_Contractor extends Application_Model_Base_Entity
{
    use Application_Model_Entity_PriorityTrait;

    protected $_titleColumn = 'description';
    public $event = '';
    protected $_RAVendor;

    /**
     * @return Application_Model_Entity_Accounts_Reserve_Contractor
     */
    public function save()
    {
        $model = new Application_Model_Entity_Accounts_Reserve();
        if (!$this->getEntityId()) {
            if ($this->getContractorEntityId()) {
                $this->setEntityId($this->getContractorEntityId());
            }
        }
        //        if ($priority = (new Application_Model_Entity_Accounts_Reserve_Contractor())->getCollection()->addUniqueFilter($this)->setOrder('id', Application_Model_Base_Collection::SORT_ORDER_ASC)->getField('priority')) {
        //            $this->setPriority(array_pop($priority) + 1);
        //        }
        $model->setData(
            array_merge(
                $this->getData(),
                ['id' => $this->getReserveAccountId()]
            )
        );
        $this->setReserveAccountId($model->save()->getId());
        parent::save();
        $this->getResource()->updateReserveAccountVendorInitialBalance($model->getId());
        $this->getResource()->updateReserveAccountVendorCurrentBalance($model->getId());

        return $this;
    }

    public function load($id, $field = null)
    {
        parent::load($id, $field);
        $basicModel = new Application_Model_Entity_Accounts_Reserve();
        $basicData = $basicModel->load($this->getReserveAccountId())->getData();
        $this->setData(array_merge($basicData, $this->getData()));

        return $this;
    }

    /**
     * @return Application_Model_Entity_Accounts_Reserve_Contractor
     */
    public function delete()
    {
        parent::delete();
        $model = new Application_Model_Entity_Accounts_Reserve();
        $model->load($this->getReserveAccountId());
        $model->delete();

        return $this;
    }

    protected function _beforeDelete()
    {
        parent::_beforeDelete();
        $reserveVendorAccountEntity = new Application_Model_Entity_Accounts_Reserve_Vendor();
        $reserveVendorAccountEntity->load($this->getReserveAccountVendorId());
        $reserveVendorAccountEntity->setCurrentBalance(
            $reserveVendorAccountEntity->getCurrentBalance() - $this->getCurrentBalance()
        );
        $reserveVendorAccountEntity->save();
    }

    /**
     * @return Application_Model_Entity_Accounts_Reserve_Vendor
     */
    public function getVendorAccount()
    {
        if (!$this->_RAVendor) {
            $this->_RAVendor = new Application_Model_Entity_Accounts_Reserve_Vendor();
            $this->_RAVendor->load($this->getReserveAccountVendorId());
        }

        return $this->_RAVendor;
    }

    /**
     * @return Application_Model_Entity_Accounts_Reserve
     */
    public function getReserveAccountEntity()
    {
        return (new Application_Model_Entity_Accounts_Reserve())->load($this->getReserveAccountId());
    }

    public function setDefaultValues()
    {
        $this->setReserveAccountVendor($this->getVendorAccount()->getReserveAccountId());
        $this->setVendorName($this->getVendorAccount()->getDefaultValues()->getEntityIdTitle());
        $this->setReserveCode($this->getVendorReserveCode());

        return $this;
    }

    public function getContractorAccountBalances($cycleId, $contractorId = null)
    {
        if (!$contractorId) {
            $contractorId = $this->getEntityId();
        }
        $sql = 'CALL getContractorAccountBalances(?,?)';
        $stmt = $this->getResource()->getAdapter()->prepare($sql);
        $stmt->bindParam(1, $cycleId);
        $stmt->bindParam(2, $contractorId);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getAccountsBalances($cycleId, $accountId)
    {
        $sql = 'CALL getAccountsBalances(?,?)';
        $stmt = $this->getResource()->getAdapter()->prepare($sql);
        $stmt->bindParam(1, $cycleId);
        $stmt->bindParam(2, $accountId);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function reorderPriority($cycleId = null, $contractorId = null)
    {
        $contractorId = $contractorId ?: $this->getReserveAccountEntity()->getEntityId();
        $reserveAccountEntity = new Application_Model_Entity_Accounts_Reserve();

        $items = (new Application_Model_Entity_Accounts_Reserve_Contractor())->getCollection()->addContractorFilter(
            $contractorId
        )->addNonDeletedFilter()->setOrder('priority', 'asc')->getItems();

        $items = array_values($items);

        foreach ($items as $priority => $item) {
            if ($item->getData('priority') !== (int)$priority) {
                $reserveAccountEntity->updatePriority($item->getReserveAccountId(), $priority);
            }
        }

        return $this;
    }

    public function setNewPriority()
    {
        $contractor = Contractor::staticLoad($this->getReserveAccountEntity()->getEntityId(), 'entity_id');
        if ($contractor->getRacPriority() == Contractor::PRIORITY_CUSTOM) {
            $item = (new Application_Model_Entity_Accounts_Reserve_Contractor())->getCollection()->addContractorFilter(
                $contractor->getEntityId()
            )->addNonDeletedFilter()->setOrder('priority', 'desc')->getFirstItem();
            if ($item->getId()) {
                $newPriority = $item->getPriority() + 1;
                $this->getReserveAccountEntity()->setPriority($newPriority)->save();
                $this->reorderPriority();
            }
        } else {
            $vendorAccountPriority = $this->getVendorAccount()->getReserveAccountEntity()->getPriority();
            $items = (new Application_Model_Entity_Accounts_Reserve_Contractor())->getCollection()->addContractorFilter(
                $contractor->getEntityId()
            )->addNonDeletedFilter()->setOrder('rav_priority', 'asc')->getItems();

            foreach ($items as $item) {
                if ($item->getRavPriority() > $vendorAccountPriority) {
                    $priority = $item->getPriority() - 1;
                    break;
                }
            }
            if (isset($item) && (!isset($priority))) {
                $priority = $item->getPriority();
            }

            $this->getReserveAccountEntity()->setPriority($priority ?? 0)->save();
            $this->reorderPriority();
        }

        return $this;
    }

    /**
     * Adding data to reserve account history for active cycles
     *
     * @return $this
     */
    public function addToHistory()
    {
        /** @var $cycleCollection Application_Model_Entity_Collection_Settlement_Cycle */
        $cycle = new Application_Model_Entity_Settlement_Cycle();
        $reserveAccount = $this->getReserveAccountEntity();
        $cycleCollection = $cycle->getCollection();

        $cycleCollection->addCarrierFilter()->addSettlementGroupFilter()->addFilter(
            'status_id',
            Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID,
            '<'
        );

        foreach ($cycleCollection as $cycle) {
            $reserveAccountHistory = new Application_Model_Entity_Accounts_Reserve_History();
            $reserveAccountHistory->addData([
                'settlement_cycle_id' => $cycle->getId(),
                'reserve_account_id' => $this->getReserveAccountId(),
                'verify_balance' => $reserveAccount->getCurrentBalance(),
                'starting_balance' => $reserveAccount->getCurrentBalance(),
                'current_balance' => $reserveAccount->getCurrentBalance(),
            ]);
            $reserveAccountHistory->save();
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function checkPermissions()
    {
        if ($this->getDeleted()) {
            return false;
        }
        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        $reserveAccountVendor = new Application_Model_Entity_Accounts_Reserve_Vendor();
        $reserveAccountVendor->load($this->getReserveAccountVendorId());
        $carrierVendorEntity = $reserveAccountVendor->getReserveAccountEntity()->getEntity();
        if ($entityId = $user->getCarrierEntityId()) {
            if ($carrierVendorEntity->getEntityTypeId() == Application_Model_Entity_Entity_Type::TYPE_CARRIER) {
                if ($carrierVendorEntity->getId() == $entityId) {
                    return true;
                }
            } elseif ($carrierVendorEntity->getEntityTypeId() == Application_Model_Entity_Entity_Type::TYPE_VENDOR) {
                $vendor = new Application_Model_Entity_Entity_Vendor();
                $vendor->load($carrierVendorEntity->getId(), 'entity_id');
                if ($vendor->getCarrierId() == $entityId) {
                    return true;
                }
            }
        }
        if ($user->isContractor()) {
            if ($this->getReserveAccountEntity()->getEntityId() == $user->getEntityId()) {
                return true;
            }
        }
        if ($user->isVendor()) {
            $contractorVendorEntity = new Application_Model_Entity_Entity_ContractorVendor();
            $contractorVendorEntity->load([
                'contractor_id' => $this->getEntityId(),
                'vendor_id' => $user->getEntityId(),
            ]);

            if ($contractorVendorEntity->getStatus(
            ) == Application_Model_Entity_System_VendorStatus::STATUS_ACTIVE || $contractorVendorEntity->getStatus(
            ) == Application_Model_Entity_System_VendorStatus::STATUS_RESCINDED) {
                return true;
            }
        }

        return false;
    }

    public function isDeletable()
    {
        $result = false;
        if ($id = $this->getReserveAccountId()) {
            $sql = 'CALL getTransactionAndDeductionTemplateCount(?)';
            $stmt = $this->getResource()->getAdapter()->prepare($sql);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            $result = $stmt->fetchAll();
            if (isset($result[0]['transaction_count'])) {
                $result = !(bool)($result[0]['transaction_count']);
            }
        }

        return $result;
    }
}
