<?php

use Application_Model_Entity_Deductions_Setup as Setup;
use Application_Model_Entity_System_VendorStatus as VendorStatus;

/**
 * @method $this staticLoad($id, $field = null)
 * @method Application_Model_Entity_Collection_Deductions_Deduction getCollection()
 * @method Application_Model_Resource_Deductions_Deduction getResource()
 */
class Application_Model_Entity_Deductions_Deduction extends Application_Model_Base_Entity
{
    use Application_Model_Entity_CycleDataTrait;
    // use Application_Model_Entity_PriorityTrait;
    use Application_Model_Entity_SettlementCycleTrait;
    use Application_Model_Entity_Permissions_CarrierVendorTrait;
    use Application_Model_RecurringTrait;
    use Application_Model_Recurring_RecurringStrategyTrait;
    use Application_Model_Entity_SettlementCycleString;
    use Application_Model_Entity_Deductions_HoldTrait;

    final public const ELIGIBLE_TYPE = 1;
    public $grids = [];

    /**
     * @return Application_Model_Base_Entity|null
     */
    public function getStatus()
    {
        if ($statusId = $this->getData('status')) {
            $statusModel = new Application_Model_Entity_System_SettlementCycleStatus();

            return $statusModel->load($statusId);
        } else {
            return null;
        }
    }

    /**
     * returns carrier
     *
     * @return Application_Model_Entity_Entity_Carrier
     */
    public function getCarrier()
    {
        $carrier = new Application_Model_Entity_Entity_Carrier();

        return $carrier->load($this->getCarrierId(), 'entity_id');
    }

    /**
     * @return Application_Model_Entity_Deductions_Setup
     */
    public function getSetup()
    {
        $deductionSetup = new Setup();

        return $deductionSetup->load($this->getSetupId());
    }

    /**
     * @return Application_Model_Entity_Accounts_User|null
     */
    public function getCreatedBy()
    {
        if ($userId = $this->getData('created_by')) {
            $userModel = new Application_Model_Entity_Accounts_User();

            return $userModel->load($userId);
        } else {
            return null;
        }
    }

    /**
     * @return Application_Model_Entity_Accounts_User|null
     */
    public function getApprovedBy()
    {
        if ($userId = $this->getData('approved_by')) {
            $userModel = new Application_Model_Entity_Accounts_User();

            return $userModel->load($userId);
        } else {
            return null;
        }
    }

    // /**
    //  * reorder priority for imported deductions
    //  *
    //  * @param $cycleId
    //  * @return $this
    //  */
    // public function reorderImportedPriority($cycleId)
    // {
    //     $select = $this->getResource()->select()->distinct()->from($this->getResource(), 'contractor_id')->where(
    //         'settlement_cycle_id = ?',
    //         $cycleId
    //     )->where('deleted = 0')->where('priority IS NULL')->group('contractor_id');
    //     $contractors = $this->getResource()->getAdapter()->fetchCol($select);

    //     if ($contractors) {
    //         //            $select = $this->getResource()->select()->distinct()->from($this->getResource(),
    //         //                array('contractor_id', new Zend_Db_Expr('IFNULL(max(priority), -1) as max_priority')))
    //         //                ->where('settlement_cycle_id = ?', $cycleId)
    //         //                ->where('deleted = 0')
    //         //                ->where('contractor_id IN (?)', $contractors)
    //         //                ->group('contractor_id');
    //         //            $priorities = $this->getResource()->getAdapter()->fetchPairs($select);
    //         //

    //         foreach ($contractors as $contractorId) {
    //             $select = $this->getResource()->getAdapter()->select();
    //             $select->from(['d' => 'deductions'], ['id'])->joinLeft(
    //                 ['s' => 'deduction_setup'],
    //                 'd.setup_id = s.id',
    //                 []
    //             )->where('d.contractor_id = ?', $contractorId)->where('d.settlement_cycle_id = ?', $cycleId)
    //                 //                    ->where('d.priority IS NULL')
    //                 ->where('d.deleted = 0')->order(
    //                     [new Zend_Db_Expr('- s.priority DESC, d.invoice_date ASC, d.amount DESC')]
    //                 );
    //             $deductions = $this->getResource()->getAdapter()->fetchAll($select);

    //             foreach ($deductions as $priority => $deduction) {
    //                 $this->updatePriority($deduction['id'], $priority);
    //             }
    //         }
    //     }

    //     return $this;
    // }

    // public function reorderPriority($cycleId = null, $contractorId = null)
    // {
    //     $cycleId = $cycleId ?: $this->getSettlementCycleId();
    //     $contractorId = $contractorId ?: $this->getContractorId();
    //     /** @var  $select */
    //     $select = $this->getResource()->select()->where('settlement_cycle_id = ?', $cycleId)->where(
    //         'contractor_id = ?',
    //         $contractorId
    //     )->where('deleted = ?', 0)
    //         //            ->where('priority IS NOT NULL')
    //         ->order('priority');

    //     $items = $select->getAdapter()->query($select)->fetchAll();

    //     foreach ($items as $priority => $item) {
    //         if ($item['priority'] != $priority) {
    //             $this->updatePriority($item['id'], $priority);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Application_Model_Entity_Deductions_Deduction
     */
    public function _beforeSave()
    {
        parent::_beforeSave();

        if ($this->getIsHold()) {
            return $this;
        }

        if ($this->getReserveAccountSender() == '') {
            $this->setReserveAccountSender();
        }
        if ($this->getReserveAccountReceiver() == '') {
            $this->setReserveAccountReceiver();
        }

        if ($this->getCreatedDatetime() == null) {
            $this->setCreatedDatetime(date('Y-m-d H:i:s'));
        }

        if ($this->getCreatedBy() == null) {
            $this->setCreatedBy(
                Application_Model_Entity_Accounts_User::getCurrentUser()->getId()
            );
        }

        if ($this->getDescription() == null) {
            $this->setDescription($this->getSetup()->getDescription());
        }

        if ($this->getRate() == null) {
            $this->setRate($this->getSetup()->getRate());
        }

        $this->setRate(str_replace(',', '', (string) $this->getRate()) ?: null);
        $this->setAmount(str_replace(',', '', (string) $this->getAmount()) ?: null);
        $this->setBalance(str_replace(',', '', (string) $this->getBalance()) ?: null);
        $this->setAdjustedBalance(str_replace(',', '', (string) $this->getAdjustedBalance()) ?: null);

        if ($this->getContractorId() == null || $this->getContractorId() == '') {
            $this->setContractorId($this->getSetup()->getContractorId());
        }

        if ($this->getPowerunitId() == null || $this->getPowerunitId() == '') {
            $this->setPowerunitId($this->getSetup()->getPowerunitId());
        }

        if ($this->getApprovedBy() == null) {
            $this->unsApprovedBy();
        }

        if ($this->getApprovedDatetime() == null) {
            $this->unsApprovedDatetime();
        }

        if ($this->getSourceId() == Application_Model_Entity_File::UNSELECTED_SOURCE_ID) {
            $this->setSourceId();
        }

        if ($this->getFromPopup()) {
            if (!$this->getBillingCycleId()) {
                $this->setBillingCycleId($this->getSetup()->getBillingCycleId());
            }

            if (!$this->getFirstStartDay()) {
                $this->setFirstStartDay($this->getSetup()->getFirstStartDay());
            }

            if (!$this->getSecondStartDay()) {
                $this->setSecondStartDay($this->getSetup()->getSecondStartDay());
            }
        }

        $recurring = $this->getRecurring();
        if (!isset($recurring)) {
            $this->setRecurring(0);
        }

        //        if (!$this->getRecurring() && $this->getDeleted() == 0) {
        //            if ($this->getId() && $this->getOriginalData('recurring')) {
        //                $this->deleteRecurringDeductions();
        //            }
        //        }

        //        if ($this->getBillingCycleId() != $this->getOriginalData('billing_cycle_id')
        //            && $this->getOriginalData('billing_cycle_id')
        //            && $this->getDeleted() == 0
        //        ) {
        //            $this->deleteRecurringDeductions();
        //        }

        if ($this->getAdjustedBalance() === '') {
            $this->setAdjustedBalance(null);
        }

        if ((float)$this->getAmount() < (float)$this->getAdjustedBalance()) {
            $this->setAdjustedBalance($this->getAmount());
        }

        if ($this->getData('quantity') > 1_000_000) {
            $this->setData('quantity', 1_000_000);
        }
        if ($this->getData('quantity') && $this->getData('quantity') < -1_000_000) {
            $this->setData('quantity', -1_000_000);
        }

        if ($this->getData('rate') > 1_000_000) {
            $this->setData('rate', 1_000_000);
        }

        if ($this->getData('rate') && $this->getData('rate') < -1_000_000) {
            $this->setData('rate', -1_000_000);
        }

        /*if ($this->getData('adjusted_balance') > 1_000_000_000_000) {
            $this->setData('adjusted_balance', 1_000_000_000_000);
        }

        if ($this->getData('adjusted_balance') && $this->getData('adjusted_balance') < -1_000_000_000_000) {
            $this->setData('adjusted_balance', -1_000_000_000_000);
        }*/

        if ($this->getFromImport()) {
            if (!$this->getSetupId()) {
                $deductionCode = $this->getDeductionCode();
                $billingCycleId = $this->getBillingCycleId();
                $providerId = $this->getProviderId();
                if ($deductionCode && $billingCycleId && $providerId) {
                    $masterTemplate = (new Setup())->createMasterTemplate([
                        'provider_id' => $providerId,
                        'deduction_code' => $deductionCode,
                        'description' => $this->getDescription(),
                        'category' => $this->getCategory(),
                        'department' => $this->getDepartment(),
                        'disbursement_code' => $this->getDisbursementCode(),
                        'billing_cycle_id' => $billingCycleId,
                        'first_start_day' => $this->getFirstStartDay(),
                        'second_start_day' => $this->getSecondStartDay(),
                        'quantity' => 1,
                        'rate' => $this->getAmount(),
                        'recurring' => $this->getRecurring(),
                    ]);
                    if (!$masterTemplate->isEmpty()) {
                        $masterTemplate->createIndividualTemplates();
                    }
                }
            }
        } elseif ((float)$this->getAmount() !== (float)$this->getRate() * (int)$this->getQuantity()) {
            $this->resetAmount();
            $this->resetBalance();
        }

        if ($this->getInvoiceDueDate() === null || $this->getInvoiceDueDate() == '0000-00-00' || (!$this->getFromImport(
        ) && ($this->getOriginalData('invoice_date') != $this->getInvoiceDate() || $this->getOriginalData(
            'terms'
        ) != $this->getTerms()))

        ) {
            $this->setDefaultInvoiceDueDate();
        }

        if ($this->getDeleted() === Application_Model_Entity_System_SystemValues::DELETED_STATUS) {
            $this->markColumnAsDeleted($this->colDeductionCode());
        }

        return $this;
    }

    public function resetBalance()
    {
        $this->setBalance((int)$this->getQuantity() * (float)$this->getRate());
    }

    public function resetAmount()
    {
        $this->setAmount((int)$this->getQuantity() * (float)$this->getRate());
    }

    /**
     * @return bool
     */
    public function isVendorApproved()
    {
        if ($this->getProvider()->isVendor()) {
            $contractorVendor = Application_Model_Entity_Entity_ContractorVendor::staticLoad([
                'contractor_id' => $this->getContractorId(),
                'vendor_id' => $this->getProviderId(),
            ]);
            if ($contractorVendor->getId()) {
                if (in_array(
                    $contractorVendor->getStatus(),
                    [VendorStatus::STATUS_ACTIVE, VendorStatus::STATUS_RESCINDED]
                )) {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    // /**
    //  * @param $priorityArr array
    //  */
    // public function setPriority($priorityArr)
    // {
    //     foreach ($priorityArr as $priority => $id) {
    //         $this->load($id);
    //         $this->addData(
    //             [
    //                 'priority' => $priority,
    //             ]
    //         );
    //         $this->save();
    //     }
    // }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        $this->getDefaultValues();
    }

    public function getDataFromSetup()
    {
        $setup = Setup::staticLoad($this->getSetupId());

        $this->setData('description', $setup->getData('description'));
        $this->setData('category', $setup->getData('category'));
        $this->setData('department', $setup->getData('department'));
        $this->setData('gl_code', $setup->getData('gl_code'));
        $this->setData('disbursement_code', $setup->getData('disbursement_code'));
        $this->setData('terms', $setup->getData('terms'));
        $this->setData('quantity', $setup->getData('quantity'));
        $this->setData('rate', $setup->getData('rate'));
        $this->setData('amount', $setup->getData('amount'));
        $this->setData('eligible', $setup->getData('eligible'));
        $this->setData('reserve_account_receiver', $setup->getData('reserve_account_receiver'));
        $this->getRecurringDataFromSetup();

        return $this;
    }

    public function getRecurringDataFromSetup()
    {
        $setup = Setup::staticLoad($this->getSetupId());
        $this->setData('recurring', $setup->getData('recurring'));
        $this->setData('billing_cycle_id', $setup->getData('billing_cycle_id'));
        $this->setData('first_start_day', $setup->getData('first_start_day'));
        $this->setData('second_start_day', $setup->getData('second_start_day'));
        $this->setData('week_offset', $setup->getData('week_offset'));

        return $this;
    }

    /**
     * @return Application_Model_Entity_Deductions_Deduction
     */
    public function getDefaultValues()
    {
        if ($this->getId() === null) {
            $this->appendData($this->_getDataFromDeductionSetup(), true);
        }
        $this->appendCycleData();

        if (!$this->getRecurring()) {
            if ($this->getInvoiceDate() === null || $this->getInvoiceDate() == '0000-00-00') {
                $this->setInvoiceDate($this->_getDefaultInvoiceDate());
            }
            if ($this->getInvoiceDueDate() === null || $this->getInvoiceDueDate() == '0000-00-00') {
                $this->setDefaultInvoiceDueDate();
            }
        }

        if ($this->getDisbursementDate() === null || $this->getDisbursementDate() == '0000-00-00') {
            $this->setDefaultDisbursementDate();
        }
        $this->setProviderIdTitle($this->getProviderName() ?: $this->_getProviderIdTitle());
        $this->setReserveAccountReceiverTitle(
            $this->_getReserveAccountVendorTitle()
        );
        $this->setContractorName($this->_getContractorName());
        if ($this->getContractorCode() === null) {
            $this->setContractorCode($this->_getContractorCode());
        }
        if ($this->getPowerunitCode() === null) {
            $this->setPowerunitCode($this->_getPowerunitCode());
        }

        $this->setStatusName($this->getStatus());
        $this->setCreatedByName($this->getCreatedBy());
        $this->setApprovedByName($this->getApprovedBy());
        if ($this->getAdjustedBalance() == null) {
            $this->setAdjustedBalance($this->getAmount());
        }

        return $this;
    }

    public function setDefaultInvoiceDueDate(): self
    {
        $this->setInvoiceDueDate($this->_getDefaultInvoiceDueDate());

        return $this;
    }

    public function setDefaultDisbursementDate(): self
    {
        $this->setDisbursementDate($this->_getDefaultDisbursementDate());

        return $this;
    }

    /**
     * Returns invoiceDate.
     *
     * @return string
     */
    protected function _getDefaultInvoiceDate()
    {
        $invoiceDate = new Zend_Date(null, 'yyyy-MM-dd');
        $cycleEntity = new Application_Model_Entity_Settlement_Cycle();
        $cycleEntity->load($this->getSettlementCycleId());
        $cycleStartDay = new Zend_Date($cycleEntity->getCycleStartDate(), 'yyyy-MM-dd');

        $result = $invoiceDate->toString('yyyy-MM-dd');
        if ($invoiceDate->isEarlier($cycleStartDay)) {
            $result = $cycleStartDay->toString('yyyy-MM-dd');
        } else {
            $cycleCloseDate = new Zend_Date($cycleEntity->getCycleCloseDate(), 'yyyy-MM-dd');
            if ($invoiceDate->isLater($cycleCloseDate)) {
                $result = $cycleCloseDate->toString('yyyy-MM-dd');
            }
        }

        return $result;
    }

    /**
     * Returns invoiceDueDate = invoiceDate + Deduction Setup.Terms
     *
     * @return string
     */
    protected function _getDefaultInvoiceDueDate()
    {
        $invoiceDueDate = new Zend_Date($this->getInvoiceDate(), 'yyyy-MM-dd');
        if ($this->getTerms() !== null) {
            $invoiceDueDate->addDay($this->getTerms());
        }

        return $invoiceDueDate->toString('yyyy-MM-dd');
    }

    /**
     * Returns disbursementDate = Carrier.Settlement Cycle Close Date
     * + Carrier.Settlement Cycle Disbursement terms
     *
     * @return string
     */
    protected function _getDefaultDisbursementDate()
    {
        if ($this->getSourceId() && $this->getDisbursementDate()) {
            return $this->getDisbursementDate();
        } else {
            $disbursementDate = new Zend_Date();
            $settlementCycle = $this->getSettlementCycle();
            if (is_countable($settlementCycle->getData()) ? count($settlementCycle->getData()) : 0) {
                $disbursementDate->set(
                    $settlementCycle->getCycleCloseDate(),
                    'yyyy-MM-dd'
                );
                $disbursementDate->addDay($settlementCycle->getDisbursementTerms());
            }

            return $disbursementDate->toString('yyyy-MM-dd');
        }
    }

    /**
     * Returns data from related 'deduction_setup' record without 'id' field
     *
     * @return array
     */
    protected function _getDataFromDeductionSetup()
    {
        $deductionSetupEntity = new Setup();
        $deductionSetupEntity->load($this->getSetupId());
        $deductionSetupEntity->unsId();
        $deductionSetupEntity->unsDisbursementDate();
        $deductionSetupEntity->unsInvoiceDate();
        $deductionSetupEntity->unsInvoiceDueDate();
        // $deductionSetupEntity->unsPriority();

        return $deductionSetupEntity->getData();
    }

    /**
     * Returns title column for contractor entity by contractor_id
     *
     * @return string
     */
    protected function _getContractorName()
    {
        $contractorEntity = new Application_Model_Entity_Entity_Contractor();
        $contractorEntity->load($this->getContractorId(), 'entity_id');

        return $contractorEntity->getData($contractorEntity->getTitleColumn());
    }

    /**
     * Returns code column for contractor entity by contractor_id
     *
     * @return string
     */
    protected function _getContractorCode()
    {
        $contractorEntity = new Application_Model_Entity_Entity_Contractor();
        $contractorEntity->load($this->getContractorId(), 'entity_id');

        return $contractorEntity->getCode();
    }

    protected function _getPowerunitCode()
    {
        $powerunitEntity = new Application_Model_Entity_Powerunit_Powerunit();
        $powerunitEntity->load($this->getPowerunitId());

        return $powerunitEntity->getCode();
    }

    /**
     * @return Application_Model_Base_Collection
     */
    public function getReserveAccountContractor()
    {
        $receiver = $this->getReserveAccountReceiver();
        if ($receiver) {
            $reserveAccountEntity = new Application_Model_Entity_Accounts_Reserve();
            $reserveAccountEntity->load($receiver);
            $vendorAccount = $reserveAccountEntity->getVendorAccount();
            if (!is_object($vendorAccount)) {
                return null;
            }
            $reserveAccountContractorEntity = new Application_Model_Entity_Accounts_Reserve_Contractor();
            $collection = $reserveAccountContractorEntity->getCollection();
            $collection->addFilter(
                'reserve_account.entity_id',
                $this->getContractor()->getEntityId()
            );
            $collection->addFilter(
                'reserve_account_contractor.reserve_account_vendor_id',
                $vendorAccount->getId()
            );

            // $collection->setOrder('priority', 'ASC');

            return $collection;
        } else {
            return null;
        }
    }

    /**
     * @return Application_Model_Entity_Entity_Contractor
     */
    public function getContractor()
    {
        $contractorEntity = new Application_Model_Entity_Entity_Contractor();
        $contractorEntity->load($this->getContractorId(), 'entity_id');

        return $contractorEntity;
    }

    /**
     * @return string
     */
    public function getProviderName()
    {
        if (!$name = $this->getData('provider_name')) {
            $name = $this->getProvider()->getEntityByType()->getName();
        }

        return $name;
    }

    /**
     * @return mixed
     */
    public function getWithdrawalAmount()
    {
        $reserveTransactionEntity = new Application_Model_Entity_Accounts_Reserve_Transaction();
        $reserveTransactionEntity->load($this->getId(), 'deduction_id');
        if ($reserveTransactionEntity->getAmount()) {
            return $reserveTransactionEntity->getAmount();
        } else {
            return null;
        }
    }

    /**
     * Sets $this->grids
     *
     * @return $this
     */
    public function setGrids()
    {
        $deductionSetupEntity = new Setup();
        $contractorEntity = new Application_Model_Entity_Entity_Contractor();
        $this->grids = [
            [
                'tabTitle' => 'Setup',
                'columns' => $deductionSetupEntity->getResource()->getInfoFields(),
                'idField' => 'id',
                'items' => $deductionSetupEntity->getCollection()->addCarrierFilter()->getItems(),
            ],
            [
                'tabTitle' => 'Contractors',
                'columns' => $contractorEntity->getResource()->getInfoFields(),
                'idField' => 'entity_id',
                'items' => $contractorEntity->getCollection()->addVisibilityFilterForUser(
                )->addFilterByActiveCarrierContractor()->getItems(),
            ],
        ];

        return $this;
    }

    // /**
    //  * @return null|int
    //  */
    // public function getFuturePriority()
    // {
    //     $priority = null;
    //     $collection = $this->getCollection();
    //     $collection->addFilter(
    //         'deductions.provider_id',
    //         $this->getProviderId()
    //     );
    //     $collection->setOrder('deductions.priority');
    //     $currentDeduction = $collection->getFirstItem();
    //     if ($currentDeduction instanceof Application_Model_Entity_Deductions_Deduction) {
    //         $priority = $currentDeduction->getPriority();
    //     }

    //     return $priority;
    // }

    // /**
    //  * @param $priority
    //  * @return Application_Model_Entity_Deductions_Deduction
    //  */
    // public function saveWithPriority($priority)
    // {
    //     $collection = $this->getCollection();
    //     $collection->addFilter('deductions.priority', $priority, '>')->addFilter(
    //         'deductions.settlement_cycle_id',
    //         Application_Model_Entity_Accounts_User::getCurrentUser()->getEntity()->getCurrentCarrier(
    //         )->getActiveSettlementCycle()->getId()
    //     )->setOrder('deductions.priority', 'ASC');
    //     $deductions = $collection->getItems();
    //     $priority++;
    //     $this->setData('priority', $priority);
    //     $this->save();

    //     foreach ($deductions as $deduction) {
    //         if ($deduction->getPriority() == $priority) {
    //             $priority++;
    //             $deduction->setData('priority', $priority);
    //             $deduction->save();
    //         } else {
    //             return $this;
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return float
     */
    public function getDiffBalance()
    {
        return (float)$this->getBalance() - (float)$this->getAdjustedBalance();
    }

    /**
     * Returns title by provider_id
     *
     * @return string
     */
    protected function _getProviderIdTitle()
    {
        $carrierEntity = new Application_Model_Entity_Entity_Carrier();
        $vendorEntity = new Application_Model_Entity_Entity_Vendor();
        $entityEntity = new Application_Model_Entity_Entity();
        $entityEntity->load($this->getProviderId(), 'id');

        if ($entityEntity->getEntityTypeId() == Application_Model_Entity_Entity_Type::TYPE_CARRIER) {
            $searchingEntity = $carrierEntity;
        } else {
            $searchingEntity = $vendorEntity;
        }

        $searchingEntity->load($this->getProviderId(), 'entity_id');

        return $searchingEntity->getData($searchingEntity->getTitleColumn());
    }

    /**
     * Returns title by reserv_account_receiver
     *
     * @return string
     */
    protected function _getReserveAccountVendorTitle()
    {
        $title = '';
        if ($this->getReserveAccountReceiver() > 0) {
            $reserveAccountVendorEntity = new Application_Model_Entity_Accounts_Reserve_Vendor();
            $reserveAccountEntity = new Application_Model_Entity_Accounts_Reserve();
            $reserveAccountEntity->load($this->getReserveAccountReceiver());
            $title = $reserveAccountEntity->getData(
                $reserveAccountVendorEntity->getTitleColumn()
            );
        }

        return $title;
    }

    /**
     * @return Application_Model_Entity_Entity()
     */
    public function getProvider()
    {
        $entity = new Application_Model_Entity_Entity();

        return $entity->load($this->getProviderId());
    }

    /**
     * Returns carrier collection in accordance with current user role
     *
     * @return Application_Model_Entity_Collection_Entity_Carrier
     */
    public function getCarrierCollection()
    {
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();
        $carrierEntity = new Application_Model_Entity_Entity_Carrier();
        $carrierCollection = $carrierEntity->getCollection();

        if ($userEntity->getRoleId() == Application_Model_Entity_System_UserRoles::CARRIER_ROLE_ID) {
            $entityId = Application_Model_Entity_Entity::getCurrentEntity()->getId();
            $carrierCollection->addFilter('entity_id', $entityId);
        } else {
            $carrierCollection->addVisibilityFilterForUser();
        }

        return $carrierCollection;
    }

    /**
     * Returns vendor collection in accordance with current user role
     *
     * @return Application_Model_Entity_Collection_Entity_Vendor
     */
    public function getVendorCollection()
    {
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();
        $vendorEntity = new Application_Model_Entity_Entity_Vendor();
        $vendorCollection = $vendorEntity->getCollection();

        if ($userEntity->getRoleId() == Application_Model_Entity_System_UserRoles::VENDOR_ROLE_ID) {
            $vendorCollection->addFilter(
                'entity_id',
                Application_Model_Entity_Entity::getCurrentEntity()->getId()
            );
        } else {
            $vendorCollection->addVisibilityFilterForUser();
        }

        return $vendorCollection;
    }

    public function getBillingCycleOptions()
    {
        $billingCycles = new Application_Model_Entity_System_CyclePeriod();
        $billingCycles = $billingCycles->getBillingCycles(
            $this->getBillingCycleId()
        );

        return $billingCycles;
    }

    public function create($setup, $powerunitId, $contractorId, $cycleId, $invoiceDate = false)
    {
        $message = null;
        $cycle = Application_Model_Entity_Settlement_Cycle::staticLoad($cycleId);
        if (!($setup instanceof Setup)) {
            $setup = (new Setup())->load(
                ['contractor_id' => $contractorId, 'master_setup_id' => $setup]
            );
        }

        $provider = $setup->getProvider();
        if (!$provider->getId()) {
            return false;
        }
        if ($provider->getEntityTypeId() == Application_Model_Entity_Entity_Type::TYPE_VENDOR) {
            if (!Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
                Application_Model_Entity_Entity_Permissions::VENDOR_DEDUCTION_MANAGE
            )) {
                return false;
            }
            /** @var Application_Model_Entity_Collection_Entity_ContractorVendor $contractorVendor */
            $contractorVendor = (new Application_Model_Entity_Entity_ContractorVendor())->getCollection()->addFilter(
                'vendor_id',
                $provider->getId()
            )->addFilter('contractor_id', $contractorId);
            if (!$contractorVendor->count()) {
                return false;
            } elseif ($contractorVendor->getFirstItem()->getStatus() == VendorStatus::STATUS_RESCINDED) {
                $message = ['warning' => 'Rescinded'];
            }
        } elseif ($provider->getEntityTypeId() == Application_Model_Entity_Entity_Type::TYPE_CARRIER) {
            if (!Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
                Application_Model_Entity_Entity_Permissions::SETTLEMENT_DATA_MANAGE
            )) {
                return false;
            }
            $contractor = new Application_Model_Entity_Entity_Contractor();
            $contractor->load($contractorId, 'entity_id');
            if ($contractor->getCarrierStatusId() == VendorStatus::STATUS_RESCINDED) {
                $message = ['warning' => 'Rescinded'];
            } elseif ($contractor->getCarrierStatusId() != VendorStatus::STATUS_ACTIVE) {
                return false;
            }
        }
        $deduction = new self();
        $deduction->unsId();
        $deduction->setSetupId($setup->getId());
        $deduction->setSettlementCycleId($cycleId);
        $deduction->setCarrierId($deduction->getSettlementCycle()->getCarrierId());

        $deduction->setPowerunitId($powerunitId);
        $deduction->setContractorId($contractorId);
        $deduction->getDefaultValues();
        // $deduction->unsetData('priority');
        if (Zend_Date::isDate($invoiceDate)) {
            $deduction->setInvoiceDate($invoiceDate);
            $deduction->changeDateFormat('invoice_date');
        }
        if ($deduction->getRecurring()) {
            $deduction->recurring();
        }
        $deduction->resetAmount();
        $deduction->resetBalance();
        $deduction->setFromPopup(true);
        $deduction->save();
        if ($deduction->getRecurring()) {
            $deduction->applyRecurring($cycle);
        }

        return $message;
    }

    public function getDeductionAmount($forReport = false)
    {
        if (is_null($value = $this->getData('deduction_amount'))) {
            if ($forReport) {
                $balance = $this->getBalance();
            } else {
                $balance = $this->getDeductionBalance();
            }
            $value = (float)$this->getAmount() - (float)$balance;
            $this->setDeductionAmount($value);
        }

        return $value;
    }

    public function getDeductionBalance()
    {
        if (is_null($value = $this->getData('deduction_balance'))) {
            $balance = is_null($this->getAdjustedBalance()) ? $this->getBalance() : $this->getAdjustedBalance();

            return (float)$balance;
        }

        return $value;
    }

    public function getMaxAdjustedBalance()
    {
        return $this->getAmount() + $this->getAdjustedBalance();
    }

    public function changeBalanceForReport($addWithdrawals = false)
    {
        $balance = $this->getDeductionBalance();

        if ($addWithdrawals && $withdrawalAmount = $this->getData('withdrawals_amount')) {
            $balance += $withdrawalAmount;
        }
        $this->setBalance($balance);

        return $this;
    }

    public function getBalanceForRemittanceReport()
    {
        $this->changeBalanceForReport();
        if (is_null($this->getAdjustedBalance()) && !$this->getBalance()) {
            return null;
        } else {
            return $this->getBalance();
        }
    }

    public function getRecurringType()
    {
        return $this->getBillingCycleId();
    }

    public function checkCarrierVendorPermissions($checkManage = false)
    {
        $provider = $this->getProvider();
        $result = true;
        if ($this->getProvider()->getId()) {
            if ($provider->getEntityTypeId() == Application_Model_Entity_Entity_Type::TYPE_VENDOR) {
                if (Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
                    Application_Model_Entity_Entity_Permissions::VENDOR_DEDUCTION_VIEW
                )) {
                    if (!Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
                        Application_Model_Entity_Entity_Permissions::VENDOR_DEDUCTION_MANAGE
                    ) && $checkManage) {
                        $result = false;
                    }
                } else {
                    $result = false;
                }
            }
        }

        return $result;
    }

    public function getProviderCode()
    {
        return $this->getProvider()->getEntityByType()->getCode();
    }

    /**
     * @return bool
     */
    public function isValidRecurring()
    {
        $provider = $this->getProvider();
        $contractor = $this->getContractor();
        if ($provider->isCarrier()) {
            if (in_array(
                $contractor->getCarrierStatusId(),
                [VendorStatus::STATUS_ACTIVE, VendorStatus::STATUS_RESCINDED]
            )) {
                return true;
            }
        } elseif ($provider->isVendor()) {
            if (in_array(
                $contractor->getVendorStatus($provider->getId()),
                [VendorStatus::STATUS_ACTIVE, VendorStatus::STATUS_RESCINDED]
            )) {
                return true;
            }
        }

        return false;
    }
}
