<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Deductions_Deduction as Deduction;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_System_ReserveTransactionTypes as TransactionType;
use Application_Model_Entity_System_SettlementCycleStatus as CycleStatus;

/**
 * @method Application_Model_Resource_Accounts_Reserve_Transaction getResource()
 */
class Application_Model_Entity_Accounts_Reserve_Transaction extends Application_Model_Base_Entity
{
    use Application_Model_Entity_CycleDataTrait;
    use Application_Model_Entity_SettlementCycleTrait;
    use Application_Model_Entity_PriorityTrait;

    protected $reserveAccountContractor;
    final public const STATUS_APPROVED_ID = 1;

    /**
     * @return Application_Model_Entity_Accounts_Reserve_Transaction
     */
    public function _beforeSave()
    {
        parent::_beforeSave();

        if ($this->getCreatedDatetime() == null) {
            $this->setCreatedDatetime(date('Y-m-d H:i:s'));
        }

        if ($this->getCreatedBy() === User::SYSTEM_USER || ($this->getCreatedBy() == null && $this->getId())) {
            $this->setCreatedBy(null);
        } elseif ($this->getCreatedBy() == null && !$this->getId()) {
            $this->setCreatedBy(User::getCurrentUser()->getId());
        }

        if ($this->getData('disbursement_id') === '') {
            $this->unsetData('disbursement_id');
        }

        if ($this->getDeductionId() === '') {
            unset($this->_data['deduction_id']);
        }

        if ($this->getApprovedBy() == '') {
            unset($this->_data['approved_by']);
        }

        if ($this->getApprovedDatetime() == null) {
            $this->unsApprovedDatetime();
        }

        if ($this->getSourceId() == '0') {
            unset($this->_data['source_id']);
        }

        if ($this->getReserveAccountSender() == null) {
            $this->unsReserveAccountSender();
        }

        $this->setAmount(str_replace(',', '', (string) $this->getAmount()));

        if ($this->getData('amount') > 1_000_000_000_000) {
            $this->setData('amount', 1_000_000_000_000);
        }

        if ($this->getData('amount') && $this->getData('amount') < -1_000_000_000_000) {
            $this->setData('amount', -1_000_000_000_000);
        }

        $this->limitAmount();

        //$this->updateEligibleDeduction();

        //$this->unsReserveAccountVendor();

        if (!$this->getReserveAccountContractor()) {
            $this->setReserveAccountContractor($this->getReserveAccountContractorEntity()->getId());
        }

        $this->recalculateBalance();

        return $this;
    }

    public function limitAmount()
    {
        $reserveAccountEntity = $this->getReserveAccountContractorEntity();
        if (!$transactionCode = $this->getCode()) {
            $transactionCode = $reserveAccountEntity->getVendorReserveCode();
        }

        if ($this->getId() && $this->getType(
        ) == TransactionType::WITHDRAWAL && $transactionCode != 'CASH' && !$reserveAccountEntity->getAllowNegative(
        )) {
            $maxAmount = $this->getReserveAccountHistory()->getCurrentBalance() + $this->getOriginalData('amount');
            if ($this->getAmount() > $maxAmount) {
                $this->setAmount($maxAmount);
            }
        }
    }

    public function delete()
    {
        $this->setDeleted(1);
        $this->save();

        return $this;
    }

    public function updateEligibleDeduction()
    {
        if ($this->getType() == TransactionType::WITHDRAWAL && $this->getDeductionId() && $this->getId(
        ) && ($this->getAmount() != $this->getOriginalData('amount') || $this->getDeleted(
        ) != $this->getOriginalData('deleted'))) {
            $deduction = $this->getDeduction();
            $amount = $this->getDeleted() ? 0 : $this->getAmount();
            if ($amount > $this->getOriginalData('amount')) {
                $newBalance = max(0, $deduction->getBalance() - ($amount - $this->getOriginalData('amount')));
            } else {
                $newBalance = $deduction->getBalance() + ($this->getOriginalData('amount') - $amount);
            }
            $deduction->setBalance($newBalance);
            $deduction->save();
        }
    }

    /**
     * @return Application_Model_Entity_Deductions_Deduction|null
     */
    public function getDeduction()
    {
        return Deduction::staticLoad($this->getDeductionId());
    }

    public function getReserveAccountHistory()
    {
        $entity = new Application_Model_Entity_Accounts_Reserve_History();
        $entity->load([
            'reserve_account_id' => $this->getReserveAccountContractorEntity()->getId(),
            'settlement_cycle_id' => $this->getSettlementCycleId(),
        ]);

        return $entity;
    }

    public function _afterSave()
    {
        parent::_afterSave();

        $this->updateReserveAccount();

        return $this;
    }

    public function reorderImportedPriority($cycleId)
    {
        // get contractors, which has transactions with NULL priority
        $select = $this->getResource()->select()->distinct()->from($this->getResource(), 'contractor_id')->where(
            'settlement_cycle_id = ?',
            $cycleId
        )->where('deleted = 0')->where('priority IS NULL')->group('contractor_id');
        $contractors = $this->getResource()->getAdapter()->fetchCol($select);

        if ($contractors) {
            // get max value of priority for that contractors
            $select = $this->getResource()->select()->distinct()->from(
                $this->getResource(),
                ['contractor_id', new Zend_Db_Expr('IFNULL(max(priority), -1) as max_priority')]
            )->where('settlement_cycle_id = ?', $cycleId)->where('deleted = 0')->where(
                'contractor_id IN (?)',
                $contractors
            )->group('contractor_id');
            $priorities = $this->getResource()->getAdapter()->fetchPairs($select);

            foreach ($priorities as $contractorId => $maxPriority) {
                $select = $this->getResource()->getAdapter()->select();
                $select->from(['rt' => 'reserve_transaction'], ['id'])->joinLeft(
                    ['ra' => 'reserve_account'],
                    'rt.reserve_account_contractor = ra.id',
                    []
                )->joinLeft(['tt' => 'reserve_transaction_type'], 'rt.type = tt.id', [])->where(
                    'rt.contractor_id = ?',
                    $contractorId
                )->where('rt.settlement_cycle_id = ?', $cycleId)->where('rt.priority IS NULL')->where(
                    'rt.deleted = 0'
                )->order([new Zend_Db_Expr('tt.type_priority, - ra.priority DESC, rt.amount DESC')]);
                $transactions = $this->getResource()->getAdapter()->fetchAll($select);

                foreach ($transactions as $priority => $transaction) {
                    $this->updatePriority($transaction['id'], $maxPriority + $priority + 1);
                }

                $this->reorderPriority($cycleId, $contractorId);
            }
        }
    }

    public function reorderPriority($cycleId = null, $contractorId = null)
    {
        $cycleId = $cycleId ?: $this->getSettlementCycleId();
        $contractorId = $contractorId ?: $this->getContractorId();

        $items = (new Application_Model_Entity_Accounts_Reserve_Transaction())->getCollection()->addSettlementFilter(
            $cycleId
        )->addContractorFilter($contractorId)->addNonDeletedFilter()->setOrder('type_priority', 'asc')->setOrder(
            'priority',
            'asc'
        )->getItems();

        $items = array_values($items);

        foreach ($items as $priority => $item) {
            if ($item->getData('priority') != $priority) {
                $this->updatePriority($item->getId(), $priority);
            }
        }

        return $this;
    }

    public function updateReserveAccount()
    {
        $cycle = $this->getSettlementCycle();
        $reserveAccountId = $this->getReserveAccountContractor();
        $reserveAccount = new Application_Model_Entity_Accounts_Reserve();
        $reserveAccount->load($reserveAccountId);

        if ($this->getType() == TransactionType::ADJUSTMENT_INCREASE || $this->getType(
        ) == TransactionType::ADJUSTMENT_DECREASE) {
            $this->getResource()->updateReserveAccountContractorStartingBalance(
                $reserveAccountId,
                $cycle->getId()
            );
        }

        $this->getResource()->updateReserveAccountContractorCurrentBalance(
            $reserveAccountId,
            $cycle->getId()
        );

        $reserveAccount->updateSubsequentCycles($cycle);

        return $this;
    }

    /**
     * Sets the related titles by id filds
     *
     * @return Application_Model_Entity_Accounts_Reserve_Transaction
     */
    public function getDefaultData()
    {
        $this->appendCycleData();
        $this->setTypeTitle($this->_getTypeTitle());
        $this->setCreatedByTitle($this->_getCreatedByTitle());
        $this->setStatusTitle($this->_getStatusTitle());
        $this->setApprovedByTitle($this->_getApprovedByTitle());

        return $this;
    }

    /**
     * Returns string title by 'type_id' field
     *
     * @return string
     */
    private function _getTypeTitle()
    {
        $typeEntity = new TransactionType();

        return $typeEntity->load($this->getType())->getTitle();
    }

    /**
     * Returns title by 'created_by' field
     *
     * @return string
     */
    private function _getCreatedByTitle()
    {
        $userEntity = new User();
        if ($userId = $this->getCreatedBy()) {
            return $userEntity->load($userId)->getName();
        } else {
            return "System";
        }
    }

    /**
     * Returns title by 'status' field
     *
     * @return string
     */
    private function _getStatusTitle()
    {
        $typeEntity = new CycleStatus();

        return $typeEntity->load($this->getStatus())->getTitle();
    }

    /**
     * @return string Application_Model_Entity_Accounts_User->getName()
     */
    private function _getApprovedByTitle()
    {
        $userEntity = new User();

        return $userEntity->load($this->getApprovedBy())->getName();
    }

    /**
     * @return Application_Model_Entity_Accounts_Reserve
     */
    public function getSender()
    {
        $reserveAccountEntity = new Application_Model_Entity_Accounts_Reserve();
        $reserveAccountEntity->load($this->getReserveAccountContractorEntity()->getId());

        return $reserveAccountEntity;
    }

    /**
     * @return Application_Model_Entity_Accounts_Reserve
     */
    public function getReceiver()
    {
        $reserveAccountEntity = new Application_Model_Entity_Accounts_Reserve();
        $reserveAccountEntity->load($this->getReserveAccountVendor());

        return $reserveAccountEntity;
    }

    /**
     * @return string
     */
    public function getProviderName()
    {
        $entity = new Application_Model_Entity_Entity();
        $entity->load($this->getReceiver()->getEntityId());

        $currentEntity = $entity->getEntityByType();

        $currentEntity->load(
            $entity->getId(),
            'entity_id'
        );

        return $currentEntity->getName();
    }

    /**
     * @return float
     */
    public function getAmountWithSign()
    {
        if ($this->getType() == TransactionType::WITHDRAWAL) {
            return -1 * ($this->getAmount() - $this->getAdjustedBalance());
        } else {
            return 1 * ($this->getAmount() - $this->getAdjustedBalance());
        }
    }

    /**
     * @param Application_Model_Entity_Accounts_Powerunit|int
     * @param $contractor
     * @param $cycle
     * @param $type
     * @param $amount
     * @return Application_Model_Entity_Accounts_Reserve_Transaction
     */
    public function create(
        // $reserveAccountVendor,
        $reserveAccount,
        $contractor,
        $cycle,
        $type,
        $amount = 0,
        $important = false,
        $bySystem = false,
        $deductionId = false
    ) {
        if ($contractor instanceof Application_Model_Entity_Entity_Contractor) {
            $contractor = $contractor->getEntityId();
        }

        if ($cycle instanceof Application_Model_Entity_Settlement_Cycle) {
            $cycle = $cycle->getId();
        }

        if (!$reserveAccount instanceof Application_Model_Entity_Accounts_Reserve) {
            $reserveAccountEntity = new Application_Model_Entity_Accounts_Reserve();
            // $reserveAccountVendor = $reserveAccountEntity->load((int)$reserveAccountVendor);
        }

        if ($reserveAccount->getEntityId() == $contractor) {
            $reserveAccountContractor = clone $reserveAccount;
            // $reserveAccountVendorId = (new Application_Model_Entity_Accounts_Reserve_Contractor())->load(
            //     $reserveAccountContractor->getId(),
            //     'reserve_account_id'
            // )->getReserveAccountVendorId();
            // if ($reserveAccountVendorId) {
            //     $reserveAccountVendorId = (new Application_Model_Entity_Accounts_Reserve_Vendor())->load(
            //         $reserveAccountVendorId
            //     )->getReserveAccountId();
            //     $reserveAccountVendor->load($reserveAccountVendorId);
            // }
        } else {
            $reserveAccountContractor = $reserveAccount->getReserveAccountContractor($contractor);
            if ($reserveAccountContractor == null) {
                return false;
            }
            $reserveAccountEntity = new Application_Model_Entity_Accounts_Reserve();
            $reserveAccountContractor = $reserveAccountEntity->load($reserveAccount->getId());
        }
        if ($reserveAccountContractor) {
            if ($amount > 0) {
                $amount = min(
                    $amount,
                    $reserveAccountContractor->getCurrentBalance()
                );
                $reserveAccountContractor->setCurrentBalance($reserveAccountContractor->getCurrentBalance() - $amount);
                $reserveAccountContractor->save();
            }
            $transaction = new self();
            $transaction->setContractorId($contractor);
            // $transaction->setReserveAccountVendor($reserveAccountVendor->getId());
            $transaction->setReserveAccountContractor($reserveAccountContractor->getId());
            $transaction->setPowerunitId($reserveAccount->getPowerunitId());
            $transaction->setType($type);
            $transaction->setDescription($reserveAccountContractor->getDescription());
            $transaction->setReference($reserveAccountContractor->getReference() ?? 'todo SUP-937');
            $transaction->setSettlementCycleId($cycle);
            if ($type == TransactionType::CONTRIBUTION) {
                $transaction->setAmount($reserveAccountContractor->getContributionAmount());
            } else {
                $transaction->setAmount($amount);
            }
            $transaction->setCreatedDatetime((new DateTime())->format('Y-m-d'));
            if ($bySystem) {
                $transaction->setCreatedBy(User::SYSTEM_USER);
            }
            if ($deductionId) {
                $transaction->setDeductionId($deductionId);
            }
            $transaction->setVendorCode($reserveAccountContractor->getVendorCode());
            if ($transaction->getAmount() > 0 || $important) {
                //check for carrier/vendor restrictions
                $entity = new Application_Model_Entity_Entity();
                // $entity->load($reserveAccountVendor->getEntityId());
                $entity->load($reserveAccount->getEntityId());
                $restriction = false;
                // if ($entity->getEntityTypeId() == Application_Model_Entity_Entity_Type::TYPE_VENDOR) {
                //     $contractorVendorEntity = new Application_Model_Entity_Entity_ContractorVendor();
                //     $contractorVendorStatus = $contractorVendorEntity->getCollection()->addFilter(
                //         'contractor_id',
                //         $contractor
                //     )->addFilter('vendor_id', $entity->getId())->getFirstItem();
                //     if ($contractorVendorStatus->getStatus(
                //     ) == Application_Model_Entity_System_VendorStatus::STATUS_ACTIVE) {
                //         $restriction = false;
                //     }
                // } elseif ($entity->getEntityTypeId() == Application_Model_Entity_Entity_Type::TYPE_CARRIER) {
                //     $contractorEntity = new Application_Model_Entity_Entity_Contractor();
                //     $contractorEntity->load($contractor, 'entity_id');
                //     if ($contractorEntity->getCarrierStatusId(
                //     ) == Application_Model_Entity_System_VendorStatus::STATUS_ACTIVE) {
                //         $restriction = false;
                //     }
                // }
                if ($restriction == false) {
                    $transaction->save();

                    return $transaction;
                }
            }
        }

        return false;
    }

    public function getNextPriority()
    {
        $select = $this->getResource()->select()->from(
            $this->getResource(),
            [new Zend_Db_Expr('IFNULL(MAX(priority), -1)+1 AS next_priority')]
        )->where('settlement_cycle_id  = ?', $this->getSettlementCycleId())->where(
            'contractor_id = ?',
            $this->getContractorId()
        )->where('deleted = ?', 0);

        return $this->getResource()->fetchRow($select)->next_priority;
    }

    public function getReserveAccountContractorBalance()
    {
        return $this->getReserveAccountContractorEntity()->getCurrentBalance();
    }

    public function getReserveAccountContractorEntity()
    {
        if (!$this->reserveAccountContractor) {
            $raEntity = new Application_Model_Entity_Accounts_Reserve_Powerunit();
            if ($contractorId = $this->getContractorId()) {
                return $raEntity
                    ->load($contractorId, 'entity_id');
            } elseif ($id = $this->getReserveAccountContractor()) {
                return $raEntity
                    ->load($id, 'id');
            }
        }

        return $this->reserveAccountContractor;
    }

    /**
     * @param Application_Model_Entity_Accounts_Reserve_Powerunit $contractorEntity
     */
    public function setReserveAccountContractorEntity($contractorEntity)
    {
        $this->reserveAccountContractor = $contractorEntity;
    }

    /**
     * @return bool
     */
    public function checkPermissions()
    {
        if ($this->getDeleted()) {
            return false;
        }
        $user = User::getCurrentUser();
        if ($entityId = $user->getCarrierEntityId()) {
            if ($this->getSettlementCycle()->getCarrierId() == $entityId) {
                return true;
            }
        } elseif ($entityId = $user->getVendorEntityId()) {
            $reserveAccount = new Application_Model_Entity_Accounts_Reserve();
            // $reserveAccount->load($this->getReserveAccountVendor());
            if ($reserveAccount->getEntityId() == $entityId) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isAllowToDelete()
    {
        $user = User::getCurrentUser();
        $cycle = $this->getSettlementCycle();
        if ($this->checkPermissions() && $user->hasPermission(Permissions::SETTLEMENT_DATA_MANAGE) && !$user->isOnboarding(
        )) {
            if (($cycle->getStatusId() == CycleStatus::PROCESSED_STATUS_ID && in_array($this->getType(), [
                        TransactionType::CONTRIBUTION,
                        TransactionType::WITHDRAWAL,
                    ])) || ($cycle->getStatusId() == CycleStatus::VERIFIED_STATUS_ID && in_array($this->getType(), [
                        TransactionType::ADJUSTMENT_INCREASE,
                        TransactionType::ADJUSTMENT_DECREASE,
                    ]))) {
                return true;
            }
        }

        return false;
    }

    /**
     * recalculate outstanding balance
     *
     * @return void
     */
    protected function recalculateBalance()
    {
        // initial balance minus paid amount
        $balance = (float)$this->getAdjustedBalance() - (float)$this->getAmount();
        $this->setBalance($balance);
    }
}
