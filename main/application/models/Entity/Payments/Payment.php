<?php

use Application_Model_Entity_Payments_Setup as Setup;

/**
 * @method $this staticLoad($id, $field = null)
 * @method Application_Model_Resource_Payments_Payment getResource()
 */
class Application_Model_Entity_Payments_Payment extends Application_Model_Base_Entity
{
    //    use Application_Model_Entity_RecurringSoftDeleteTrait;
    use Application_Model_Entity_CycleDataTrait;
    use Application_Model_Entity_SettlementCycleTrait;
    use Application_Model_Entity_Permissions_CarrierTrait;
    use Application_Model_RecurringTrait;
    use Application_Model_Recurring_RecurringStrategyTrait;
    use Application_Model_Entity_SettlementCycleString;

    public $grids = [];
    protected $_status;
    protected $_setup;

    /**
     * @return Application_Model_Entity_System_SettlementCycleStatus|null
     */
    public function getStatus()
    {
        if (isset($this->_status)) {
            return $this->_status;
        } else {
            if ($statusId = $this->getData('status')) {
                $statusModel = new Application_Model_Entity_System_SettlementCycleStatus();

                return $this->_status = $statusModel->load($statusId);
            } else {
                return null;
            }
        }
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
     * returns payment setup
     *
     * @return Application_Model_Entity_Payments_Setup
     */
    public function getSetup()
    {
        if (!isset($this->_setup)) {
            $paymentSetup = new Setup();
            $this->_setup = $paymentSetup->load(
                $this->getSetupId()
            );
        }

        return $this->_setup;
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

    public function _afterSave()
    {
        if ($this->getRecurring()) {
            if (!$this->getRecurringParentId()) {
                $this->setRecurringParentId($this->getId());

                parent::save();
            }
        }

        return $this;
    }

    /**
     * @return Application_Model_Entity_Payments_Payment
     */
    public function _beforeSave()
    {
        parent::_beforeSave();

        if ($this->getCarrierId() == null) {
            $this->setCarrierId(
                Application_Model_Entity_Accounts_User::getCurrentUser()->getEntity()->getEntityId(
                )
            );
        }

        if ($this->getCreatedDatetime() == null) {
            $this->setCreatedDatetime(date('Y-m-d H:i:s'));
        }

        if ($this->getDescription() == null) {
            $this->setDescription($this->getSetup()->getDescription());
        }

        if ($this->getQuantity() == null) {
            $this->setQuantity(
                $this->getSetup()->getQuantity()
            );
        }

        if ($this->getRate() == null) {
            $this->setRate($this->getSetup()->getRate());
        }
        $this->setRate(str_replace(',', '', (string) $this->getRate()));

        if ($this->getApprovedBy() == null) {
            $this->unsApprovedBy();
        }

        if ($this->getApprovedDatetime() == '') {
            $this->unsApprovedDatetime();
        }

        if ($this->getCreatedBy() == null) {
            $this->setCreatedBy(
                Application_Model_Entity_Accounts_User::getCurrentUser()->getId()
            );
        }

        if ($this->getContractorId() == null || $this->getContractorId() == '') {
            $this->setContractorId($this->getSetup()->getContractorId());
        }

        if ($this->getPowerunitId() == null || $this->getPowerunitId() == '') {
            $this->setPowerunitId($this->getSetup()->getPowerunitId());
        }

        if ($this->getSourceId() == Application_Model_Entity_File::UNSELECTED_SOURCE_ID) {
            $this->setSourceId();
        }

        $quantity = $this->getData('quantity');

        if (!is_numeric($quantity)) {
            $this->setData('quantity', 0);
        } elseif ($quantity > 1_000_000) {
            $this->setData('quantity', 1_000_000);
        } elseif ($quantity < -1_000_000) {
            $this->setData('quantity', -1_000_000);
        }

        $loadedMiles = $this->getData('loaded_miles');

        if (!is_numeric($loadedMiles)) {
            $this->setData('loaded_miles', 0);
        }

        $emptyMiles = $this->getData('empty_miles');

        if (!is_numeric($emptyMiles)) {
            $this->setData('empty_miles', 0);
        }

        $rate = $this->getData('rate');

        if (!is_numeric($rate)) {
            $this->setData('rate', 0);
        } elseif ($rate > 1_000_000) {
            $this->setData('rate', 1_000_000);
        } elseif ($rate < -1_000_000) {
            $this->setData('rate', -1_000_000);
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

        if ($this->getRecurring() == null) {
            $this->setRecurring(
                $this->getSetup()->getRecurring()
            );
        }

        if (!$this->getRecurring()) {
            $this->setRecurring(0);
        }

        if ((float)$this->getAmount() !== (float)$this->getRate() * (int)$this->getQuantity() || is_null(
            $this->getAmount()
        )) {
            $this->resetAmount();
            $this->resetBalance();
        }

        if ($this->getInvoiceDueDate() === null || $this->getInvoiceDueDate() == '0000-00-00' || (!$this->getFromImport(
        ) && ($this->getOriginalData('invoice_date') != $this->getInvoiceDate() || $this->getOriginalData(
            'terms'
        ) != $this->getTerms()))) {
            $this->setDefaultInvoiceDueDate();
        }

        if ($this->getDisbursementDate() === null || $this->getDisbursementDate() === '0000-00-00') {
            $this->setDefaultDisbursementDate();
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

    protected function _afterLoad()
    {
        parent::_afterLoad();
        $this->getDefaultValues();
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

    public function getDataFromSetup()
    {
        $setup = Setup::staticLoad($this->getSetupId());

        $this->setData('carrier_payment_code', $setup->getData('carrier_payment_code'));
        $this->setData('description', $setup->getData('description'));
        $this->setData('category', $setup->getData('category'));
        $this->setData('department', $setup->getData('department'));
        $this->setData('gl_code', $setup->getData('gl_code'));
        $this->setData('disbursement_code', $setup->getData('disbursement_code'));
        $this->setData('terms', $setup->getData('terms'));
        $this->setData('quantity', $setup->getData('quantity'));
        $this->setData('rate', $setup->getData('rate'));
        $this->setData('amount', $setup->getData('amount'));
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
     * @return Application_Model_Entity_Payments_Payment
     */
    public function getDefaultValues()
    {
        if ($this->getId() === null) {
            $this->appendData($this->_getDataFromPaymentSetup(), true);
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
        $this->setContractorName($this->_getContractorName());
        if ($this->getContractorCode() === null) {
            $this->setContractorCode($this->_getContractorCode());
        }
        $this->setPowerunitCode($this->_getPowerunitCode());
        $this->setStatusName($this->getStatus());
        $this->setCreatedByName($this->getCreatedBy());
        $this->setApprovedByName($this->getApprovedBy());

        return $this;
    }

    /**
     * Sets calculated invoice due date
     *
     * @return Application_Model_Entity_Payments_Payment
     */
    public function setDefaultInvoiceDueDate()
    {
        $this->setInvoiceDueDate($this->_getDefaultInvoiceDueDate());

        return $this;
    }

    /**
     * Sets calculated disbursement date
     *
     * @return Application_Model_Entity_Payments_Payment
     */
    public function setDefaultDisbursementDate()
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
        $cycleCloseDate = new Zend_Date($cycleEntity->getCycleCloseDate(), 'yyyy-MM-dd');

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
     * Returns invoiceDueDate = invoiceDate + Payment Setup.Terms
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

    /**
     * Returns data from related 'payment_setup' record without 'id' field
     *
     * @return array
     */
    protected function _getDataFromPaymentSetup()
    {
        $paymentSetupEntity = new Setup();
        $paymentSetupEntity->load($this->getSetupId());
        $paymentSetupEntity->unsId();
        $paymentSetupEntity->unsDisbursementDate();
        $paymentSetupEntity->unsInvoiceDate();
        $paymentSetupEntity->unsInvoiceDueDate();

        return $paymentSetupEntity->getData();
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
     * Sets $this->grids
     *
     * @return $this
     */
    public function setGrids()
    {
        $paymentSetupEntity = new Setup();
        $contractorEntity = new Application_Model_Entity_Entity_Contractor();
        $this->grids = [
            [
                'tabTitle' => 'Setup',
                'columns' => $paymentSetupEntity->getResource()->getInfoFields(),
                'idField' => 'id',
                'items' => $paymentSetupEntity->getCollection()->addCarrierFilter()->getItems(),
            ],
            [
                'tabTitle' => 'Contractors',
                'columns' => $contractorEntity->getResource()->getInfoFields(),
                'idField' => 'entity_id',
                'items' => $contractorEntity->getCollection()->addFilterByActiveCarrierContractor()->getItems(),
            ],
        ];

        return $this;
    }

    public function getBillingCycleOptions()
    {
        $billingCycles = new Application_Model_Entity_System_CyclePeriod();
        $billingCycles = $billingCycles->getBillingCycles(
            $this->getBillingCycleId()
        );

        return $billingCycles;
    }

    public function getRecurringType()
    {
        return $this->getBillingCycleId();
    }

    public function isValidRecurring()
    {
        return true;
    }
}
