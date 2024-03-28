<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Collection_Transactions_Disbursement as DisbursementCollection;
use Application_Model_Entity_Deductions_Deduction as Deduction;
use Application_Model_Entity_Settlement_Rule as CycleRule;
use Application_Service_Hub as Hub;
use Application_View_Helper_FlashMessage as FlashMessage;

/**
 * @method $this staticLoad($id, $field = null)
 */
class Application_Model_Entity_Settlement_Cycle extends Application_Model_Base_Entity
{
    use Application_Model_RecurringTrait;

    final public const CYCLE_STAGE_ERROR = 'Cycle stage error';
    final public const ONLY_ACTIVE = true;
    final public const ARCHIVE_FILTER_TYPE = 1;
    final public const ALL_FILTER_TYPE = 2;
    final public const LAST_CLOSED_FILTER_TYPE = 3;
    final public const CURRENT_FILTER_TYPE = 4;
    final public const ALL_FILTER_TYPE_ASC = 5;
    final public const ALL_FOR_REPORT_FILTER_TYPE = 6;
    final public const ALL_FOR_IMPORTING_FILTER_TYPE = 7;
    final public const VERIFIED_FILTER_TYPE = 8;
    final public const PROCESSING_FILTER_TYPE = 9;
    protected $_carrier;
    protected $_processModel;
    protected $_titleColumn = 'cycle_start_date';
    protected $_cycleCollection;
    protected $_totals;
    protected $_rule;
    protected $_status;
    public $paymentsAndDeductions;

    public function _beforeDelete()
    {
        foreach ($this->getReserveTransactions() as $transaction) {
            $transaction->delete();
        }
        foreach ($this->getDeductions() as $deduction) {
            $deduction->delete();
        }

        foreach ($this->getPayments() as $payment) {
            $payment->delete();
        }

        parent::_beforeDelete();

        return $this;
    }

    /**
     *
     */
    public function _beforeSave()
    {
        if ($this->getId() && User::getCurrentUser()->isAdmin(
        ) && $this->getOriginalData('cycle_close_date') != $this->getCycleCloseDate() && $this->getCycleCloseDate(
        )) {
            $this->setIsCustomCloseDate(true);
        }

        if ($this->getStatusId() == null) {
            $this->setStatusId(
                Application_Model_Entity_System_SettlementCycleStatus::NOT_VERIFIED_STATUS_ID
            );
        }

        if ($this->getCycleCloseDate() == null || $this->getOriginalData(
            'cycle_start_date'
        ) != $this->getCycleStartDate()) {
            if (!$this->getIsCustomCloseDate()) {
                $this->_updateCycleCloseDate();
            }

            if ($this->getProcessingDate() == null || $this->getOriginalData(
                'processing_date'
            ) == $this->getProcessingDate()) {
                $this->updateProcessingDate();
            }

            if ($this->getDisbursementDate() == null || $this->getOriginalData(
                'disbursement_date'
            ) == $this->getDisbursementDate()) {
                $this->updateDisbursementDate();
            }
        }

        if ($this->getCarrierId() == null) {
            $this->setCarrierId(
                User::getCurrentUser()->getEntity()->getCurrentCarrier()->getEntityId(
                )
            );
        }

        if (is_null($this->getPaymentTerms())) {
            $this->setPaymentTerms($this->getRule()->getPaymentTerms());
        }

        if (is_null($this->getDisbursementTerms())) {
            $this->setDisbursementTerms($this->getRule()->getDisbursementTerms());
        }

        //        if (
        //            $this->getCyclePeriodId()
        //            != Application_Model_Entity_System_CyclePeriod::
        //            SEMY_MONTHLY_PERIOD_ID
        //        ) {
        //
        //            $this->setFirstStartDay();
        //            $this->setSecondStartDay();
        //        }

        if ($this->getStatusId() == Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID) {
            $this->setApprovedBy(
                User::getCurrentUser()->getId()
            );
            $this->setApprovedDatetime(date('Y-m-d H:i:s'));
        }
        parent::_beforeSave();

        return $this;
    }

    public function _afterSave()
    {
        parent::_afterSave();
        $currentCycle = User::getCurrentUser()->getCurrentCycle();
        if ($currentCycle->getId() == $this->getId()) {
            $currentCycle->addData($this->getData());
        }

        return $this;
    }

    /**
     * @return Application_Model_Base_Entity
     */
    public function getSettlementCycle()
    {
        $cycleModel = new Application_Model_Entity_System_CyclePeriod();

        return $cycleModel->load($this->getCyclePeriodId());
    }

    /**
     * @return mixed
     */
    public function getSettlementDayWord()
    {
        return Application_Model_System_Daysofweek::getDayByNumber(
            $this->getSettlementDay()
        );
    }

    /**
     * @return Application_Model_Entity_System_SettlementCycleStatus
     */
    public function getStatus()
    {
        if (!isset($this->_status) || $this->_status->getId() != $this->getStatusId()) {
            $statusModel = new Application_Model_Entity_System_SettlementCycleStatus();
            $this->_status = $statusModel->load($this->getStatusId());
        }

        return $this->_status;
    }

    public function getCyclePeriod()
    {
        $periodModel = new Application_Model_Entity_System_CyclePeriod();

        return $periodModel->load($this->getCyclePeriodId());
    }

    public function process()
    {
        $this->checkCurrentStatus(__FUNCTION__);
        $this->updateReserveAccountContractorProcess();
        $this->getProcessModel()->process();
        $this->setStatusId(Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID);
        $this->save();
    }

    public function verify()
    {
        $this->checkCurrentStatus(__FUNCTION__);
        //executes verify logics
        $this->getProcessModel()->verify();
        $this->applyRecurringPayments();
        $this->applyRecurringDeductions();
        $this->applyHoldDeductions();
        $this->updateReserveAccountContractorAfterVerify();
        //update status
        $this->setStatusId(Application_Model_Entity_System_SettlementCycleStatus::VERIFIED_STATUS_ID);
        $this->save();

        return new FlashMessage(
            message: 'Settlement Cycle was successfully verified.',
            type: FlashMessage::T_OK
        );
    }

    public function approve()
    {
        $this->checkCurrentStatus(__FUNCTION__);
        $this->getProcessModel()->approve();
        $this->setStatusId(Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID);
        $this->save();

        CycleRule::staticLoad($this->getCarrierId(), 'carrier_id')->setData(
            'last_closed_cycle_id',
            $this->getId()
        )->save();

        $this->saveHistory();
        $user = User::getCurrentUser();
        $credentials = $user->getCredentials();
        //        $result = exec('php ' . APPLICATION_PATH . '/../scripts/report.php -a settlement/' . $this->getId() . ' -e ' . APPLICATION_ENV/*.' > /dev/null &'*/);
        $command = 'cd ' . APPLICATION_PATH . '/../scripts/; php report.php -a settlement/' . $this->getId(
        ) . ' -u ' . $user->getId(
        ) . ' -t ' . $credentials['token'] . ' -s ' . $credentials['secret'] . ' -e ' . APPLICATION_ENV . ' &> /dev/null &';
        exec($command);

        # export settlement data to Hub
        $this->export_to_hub();

        return $this;
    }

    public function delete()
    {
        $this->checkCurrentStatus(__FUNCTION__);

        parent::delete();

        return $this;
    }

    public function checkCurrentStatus($action)
    {
        if ($this->getCarrierId() != User::getCurrentUser()->getCarrierEntityId()) {
            throw new Exception(self::CYCLE_STAGE_ERROR);
        }
        $currentStatusId = $this->getStatusId();
        switch ($action) {
            case 'process':
                if ($currentStatusId != Application_Model_Entity_System_SettlementCycleStatus::VERIFIED_STATUS_ID) {
                    throw new Exception(self::CYCLE_STAGE_ERROR);
                }
                break;
            case 'delete':
                if ($currentStatusId != Application_Model_Entity_System_SettlementCycleStatus::VERIFIED_STATUS_ID) {
                    throw new Exception(self::CYCLE_STAGE_ERROR);
                }
                break;
            case 'clear':
                if ($currentStatusId != Application_Model_Entity_System_SettlementCycleStatus::VERIFIED_STATUS_ID) {
                    throw new Exception(self::CYCLE_STAGE_ERROR);
                }
                break;
            case 'verify':
                if ($currentStatusId != Application_Model_Entity_System_SettlementCycleStatus::NOT_VERIFIED_STATUS_ID) {
                    throw new Exception(self::CYCLE_STAGE_ERROR);
                }
                break;
            case 'approve':
                if ($currentStatusId != Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID) {
                    throw new Exception(self::CYCLE_STAGE_ERROR);
                }
                foreach ($this->getSettlementContractors() as $contractor) {
                    if ($contractor['settlement'] < 0) {
                        throw new Exception(self::CYCLE_STAGE_ERROR);
                    }
                }
                break;
            case 'reject':
                if ($currentStatusId != Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID) {
                    throw new Exception(self::CYCLE_STAGE_ERROR);
                }
                break;
            case 'close':
            case 'export_to_hub':
                if ($currentStatusId != Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID) {
                    throw new Exception(self::CYCLE_STAGE_ERROR);
                }
                break;
            default:
                throw new Exception(self::CYCLE_STAGE_ERROR);
        }
    }

    /**
     * returns processModel
     *
     * @return Application_Model_Calculations_Settlement
     */
    public function getProcessModel()
    {
        if ($this->_processModel == null) {
            $this->_processModel = new Application_Model_Calculations_Settlement($this);
        }

        return $this->_processModel;
    }

    /**
     * update Cycle Close Date
     *
     * @return Application_Model_Entity_Settlement_Cycle
     */
    protected function _updateCycleCloseDate()
    {
        $periodModel = new Application_Model_Entity_System_CyclePeriod();
        $periodModel->load($this->getCyclePeriodId());
        if ($periodModel->getId()) {
            $closeDate = $periodModel->getPeriodLength($this);
            $startDate = new Zend_Date($this->getCycleStartDate(), Zend_Date::ISO_8601);
            if (!$this->getId() || !$this->getCycleCloseDate()) {
                $ruleStartDate = new Zend_Date($this->getRule()->getCycleStartDate(), Zend_Date::ISO_8601);
                if ($ruleStartDate->isLater($startDate, Zend_Date::DATES) && ($ruleStartDate->equals(
                    $closeDate,
                    Zend_Date::DATES
                ) || $ruleStartDate->isEarlier($closeDate, Zend_Date::DATES))) {
                    $closeDate = clone($ruleStartDate);
                    $closeDate->subDay(1);
                }
            }
            $this->setCycleCloseDate($closeDate->toString('yyyy-MM-dd'));
        }

        return $this;
    }

    /**
     * returns Carrier
     *
     * @return Application_Model_Entity_Entity_Carrier
     */
    public function getCarrier()
    {
        if ($this->_carrier == null) {
            $carrierModel = new Application_Model_Entity_Entity_Carrier();
            $this->_carrier = $carrierModel->load($this->getCarrierId(), 'entity_id');
        }

        return $this->_carrier;
    }

    /**
     * @param array $filter
     * @return Application_Model_Entity_Collection_Payments_Payment
     */
    public function getPayments($filter = [])
    {
        $payments = $this->getProcessModel()->getPayments();
        if (!empty($filter)) {
            foreach ($filter as $field => $value) {
                $payments->addFilter($field, $value);
            }
        }

        return $payments;
    }

    //    /**
    //     * @return bool
    //     */
    //    public function hasNotApprovedPayments()
    //    {
    //        return $this->getProcessModel()->hasNotApprovedPayments();
    //    }

    /**
     * @param array $filter
     * @return Application_Model_Entity_Collection_Deductions_Deduction
     */
    public function getDeductions($filter = [])
    {
        $deductions = $this->getProcessModel()->getDeductions();
        if (!empty($filter)) {
            foreach ($filter as $field => $value) {
                $deductions->addFilter($field, $value);
            }
        }

        return $deductions;
    }

    /**
     * @return Application_Model_Entity_Collection_Transactions_Disbursement
     */
    public function getDisbursement()
    {
        $entity = new Application_Model_Entity_Transactions_Disbursement();

        return $entity->getCollection()->addFilter('settlement_cycle_id', $this->getId());
    }

    /**
     * @return int
     */
    public function getDisbursementRoutingCheckSum()
    {
        return 0;
    }

    public function getDisbursementAmountSum()
    {
        $amountSum = 0;
        foreach ($this->getDisbursement() as $disbursement) {
            /**
             * @var Application_Model_Entity_Transactions_Disbursement $disbursement
             */
            $amountSum += $disbursement->getAmount();
        }

        return $amountSum;
    }

    //    /**
    //     * @return int
    //     */
    //    public function hasNotApprovedDeductions()
    //    {
    //        return $this->getProcessModel()->hasNotApprovedDeductions();
    //    }

    /**
     * @param array $filter
     * @return Application_Model_Entity_Collection_Accounts_Reserve_Transaction
     */
    public function getReserveTransactions($filter = [])
    {
        $reserveTransactions = $this->getProcessModel()->getReserveTransactions();
        if (!empty($filter)) {
            foreach ($filter as $field => $value) {
                if ($field == 'reserve_account_contractor' || $field == 'reserve_account_vendor') {
                    $reserveAccountEntity = new Application_Model_Entity_Accounts_Reserve();
                    $reserveAccountArray = $reserveAccountEntity->getCollection()->addFilter(
                        'entity_id',
                        $value
                    )->getField('id');
                    if ($reserveAccountArray == []) {
                        array_push($reserveAccountArray, '0');
                    }
                    $reserveTransactions->addFilter(
                        $field,
                        $reserveAccountArray,
                        'IN'
                    );
                } else {
                    $reserveTransactions->addFilter($field, $value);
                }
            }
        }

        return $reserveTransactions;
    }

    /**
     * @return Application_Model_Entity_Collection_Accounts_Reserve_Transaction
     */
    public function getWithdrawals()
    {
        return $this->getProcessModel()->getWithdrawals();
    }

    /**
     * @return Application_Model_Entity_Collection_Accounts_Reserve_Transaction
     */
    public function getContributions()
    {
        return $this->getProcessModel()->getContributions();
    }

    /**
     * @return array
     */
    public function getCycleContractorsCollection()
    {
        $contractorIds = [];
        foreach ($this->getSettlementContractors() as $contractor) {
            $contractorIds[] = $contractor['id'];
        }
        $contractors = [];
        if ($contractorIds) {
            $contractor = new Application_Model_Entity_Entity_Contractor();
            $contractors = $contractor->getCollection()->addFilter('entity_id', $contractorIds, 'IN', false)->getItems(
            );
        }

        return $contractors;
    }

    public function getSettlementContractors(
        $order = 'id',
        $direction = 'ASC',
        $filterParams = null,
        $contractor = null,
        $limit = null,
        $offset = null
    ) {
        $id = $this->getId();
        $sql = 'CALL getSettlementContractors(?,?,?,?,?,?,?)';
        $filter = '';
        $numberFields = [
            'payments',
            'deductions_balance',
            'deductions_amount',
            'settlement_group',
            'deductions_adjusted_balance',
            'contribution',
            'withdrawal',
            'settlement',
        ];
        if (is_array($filterParams)) {
            foreach ($filterParams as $p_name => $p_val) {
                if ($p_name != 'sort' && $p_name != 'order') {
                    if (in_array($p_name, $numberFields)) {
                        $filter .= " AND " . 'm.' . $p_name . " = '" . $p_val . "' ";
                    } else {
                        $filter .= " AND " . 'LOWER(m.' . $p_name . ") LIKE LOWER('%" . $p_val . "%') ";
                    }
                }
            }
        }
        if ($filter) {
            $filter = 'WHERE ' . substr($filter, 4);
        }
        $stmt = $this->getResource()->getAdapter()->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $order);
        $stmt->bindParam(3, $direction);
        $stmt->bindParam(4, $filter);
        $stmt->bindParam(5, $contractor);
        $stmt->bindParam(6, $limit);
        $stmt->bindParam(7, $offset);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getSettlementContractorsCount(
        $order = 'id',
        $direction = 'ASC',
        $filterParams = null,
        $contractor = null
    ) {
        $id = $this->getId();
        $sql = 'CALL getSettlementContractorsCount(?,?,?,?,?)';
        $filter = '';
        $numberFields = [
            'payments',
            'deductions_balance',
            'deductions_amount',
            'contribution',
            'withdrawal',
            'settlement',
        ];
        if (is_array($filterParams)) {
            foreach ($filterParams as $p_name => $p_val) {
                if ($p_name != 'sort' && $p_name != 'order') {
                    if (in_array($p_name, $numberFields)) {
                        $filter .= " AND " . 'm.' . $p_name . " = '" . $p_val . "' ";
                    } else {
                        $filter .= " AND " . 'LOWER(m.' . $p_name . ") LIKE LOWER('%" . $p_val . "%') ";
                    }
                }
            }
        }
        if ($filter) {
            $filter = 'WHERE ' . substr($filter, 4);
        }
        $stmt = $this->getResource()->getAdapter()->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $order);
        $stmt->bindParam(3, $direction);
        $stmt->bindParam(4, $filter);
        $stmt->bindParam(5, $contractor);
        $stmt->execute();

        $result = $stmt->fetchAll();
        $count = 0;
        if (isset($result[0]['cnt'])) {
            $count = (int)$result[0]['cnt'];
        }

        return $count;
    }

    public function updateReserveAccountContractorAfterVerify()
    {
        $id = $this->getId();
        $sql = 'CALL updateRACVerify(?)';
        $stmt = $this->getResource()->getAdapter()->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $this;
    }

    public function updateReserveAccountContractorAfterClear()
    {
        $id = $this->getId();
        $sql = 'CALL updateRACClear(?)';
        $stmt = $this->getResource()->getAdapter()->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $this;
    }

    public function updateReserveAccountContractorProcess()
    {
        $id = $this->getId();
        $sql = 'CALL updateRACProcess(?)';
        $stmt = $this->getResource()->getAdapter()->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $this;
    }

    public function updateReserveAccountVendorProcess()
    {
        $collection = (new Application_Model_Entity_Accounts_Reserve_Vendor())->getCollection();
        $collection->addCarrierVendorFilter(false, true)->addNonDeletedFilter();
        foreach ($collection as $item) {
            $item->updateCurrentBalance();
        }
    }

    public function getSettlementVendors()
    {
        $id = $this->getId();
        $sql = 'CALL getSettlementVendors(?)';
        $stmt = $this->getResource()->getAdapter()->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getSettlementContractorsTotal($filterParams = null, $limit = null, $offset = null)
    {
        $filter = '';
        $numberFields = [
            'payments',
            'deductions_balance',
            'deductions_amount',
            'contribution',
            'withdrawal',
            'settlement',
        ];
        if (is_array($filterParams)) {
            foreach ($filterParams as $p_name => $p_val) {
                if ($p_name != 'sort' && $p_name != 'order') {
                    if (in_array($p_name, $numberFields)) {
                        $filter .= " AND " . 'm.' . $p_name . " = '" . $p_val . "' ";
                    } else {
                        $filter .= " AND " . 'LOWER(m.' . $p_name . ") LIKE LOWER('%" . $p_val . "%') ";
                    }
                }
            }
        }
        if ($filter) {
            $filter = 'WHERE ' . substr($filter, 4);
        }
        $id = $this->getId();
        $sql = 'CALL getSettlementContractorsTotal(?,?,?,?)';
        $stmt = $this->getResource()->getAdapter()->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->bindParam(2, $filter);
        $stmt->bindParam(3, $limit);
        $stmt->bindParam(4, $offset);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function getSettlementPowerunitTotalsByPeriod($from_date, $until_date)
    {
        $sql = 'CALL getSettlementPowerunitTotalsByPeriod(?,?,?)';
        $stmt = $this->getResource()->getAdapter()->prepare($sql);
        $stmt->bindParam(1, $from_date);
        $stmt->bindParam(2, $until_date);
        $stmt->bindParam(3, $this->getId()); // cycle ID
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getTotal($name)
    {
        $totals = $this->getSettlementContractorsTotal();

        return $totals[$name] ?? false;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        $result = [];
        if ($this->getData(
            'status_id'
        ) != Application_Model_Entity_System_SettlementCycleStatus::NOT_VERIFIED_STATUS_ID) {
            $result = $this->getProcessModel()->getResult();
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isFullyApproved()
    {
        return true;
    }

    /**
     * return active vendors in current cycle
     *
     * @return Application_Model_Entity_Collection_Entity_Vendor
     */
    public function getVendors(
        Application_Model_Entity_Entity_Contractor $contractor
    ) {
        $deductions = $this->getDeductions()->addFilter(
            'deductions.contractor_id',
            $contractor->getEntityId()
        );
        $providers = $deductions->getField('provider_id');
        $vendorEntity = new Application_Model_Entity_Entity_Vendor();
        $vendorCollection = $vendorEntity->getCollection()->addFilter(
            'vendor.entity_id',
            $providers,
            'IN'
        );

        return $vendorCollection;
    }

    public function getContractorsArray()
    {
        $contractorsArray = [];
        $contractorEntity = new Application_Model_Entity_Entity_Contractor();
        $contractorItems = $contractorEntity->getCollection()->addVisibilityFilterForUser()->getItems();
        //$this->getProcessModel()->get
        foreach ($contractorItems as $contractor) {
            $contractorsArray[$contractor->getEntityId()] = $contractor->getCompanyName();
        }

        return $contractorsArray;
    }

    /**
     * return true if this cycle first for current carrier
     *
     * @return bool
     */
    public function isFirstCycle()
    {
        $firstCycle = $this->getCollection()->addFilter(
            'settlement_cycle.carrier_id',
            $this->getCarrierId()
        )->getFirstItem();
        if ($firstCycle instanceof Application_Model_Entity_Settlement_Cycle) {
            if ($firstCycle->getId() == $this->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getAllCyclePeriods($closedCount = 3, $singleArray = false)
    {
        $periodsArray = [];
        if ($singleArray) {
            $closedPeriods = $this->getCyclePeriods(self::ALL_FILTER_TYPE);
        } else {
            $closedPeriods = $this->getCyclePeriods(
                self::LAST_CLOSED_FILTER_TYPE
            );
        }

        $closed = [];
        $archived = [];
        foreach ($closedPeriods as $key => $value) {
            if (count($closed) <= $closedCount) {
                $closed[$key] = $value;
            } else {
                $period = explode(' - ', (string) $value);
                if (count($period) == 2) {
                    $startDate = new Zend_Date($period[0], 'MM/dd/yyyy');
                    $year = $startDate->toString('yyyy');
                } else {
                    $year = 'not exist';
                }
                $archived[$year][$key] = $value;
            }
        }

        if (!$closed) {
            $closed[0] = 'not exist';
        }

        if (!$archived) {
            $archived['none'][0] = 'not exist';
        }
        $periodsArray[self::ARCHIVE_FILTER_TYPE] = $archived;
        $periodsArray[self::LAST_CLOSED_FILTER_TYPE] = $closed;
        if (!$singleArray) {
            $periodsArray[self::CURRENT_FILTER_TYPE] = $this->getCyclePeriods(
                self::CURRENT_FILTER_TYPE
            );
        }

        return $periodsArray;
    }

    /**
     * @return array
     */
    public function getAllCyclePeriodsForReport()
    {
        $periodsArray = [];
        $periods = $this->getCyclePeriods(self::ALL_FOR_REPORT_FILTER_TYPE, false, true);

        foreach ($periods as $id => $value) {
            $period = explode(' - ', (string) $value['title']);
            if (count($period) == 2) {
                $startDate = new Zend_Date($period[0], 'MM/dd/yy');
                $year = $startDate->toString('yyyy');
            } else {
                $year = 'not exist';
            }
            $periodsArray[$year][$id] = $value;
        }

        if (empty($periodsArray)) {
            $periodsArray['none'][0]['title'] = 'not exist';
            $periodsArray['none'][0]['status'] = 0;
        }

        return $periodsArray;
    }

    public function getAllCyclePeriodsForImporting()
    {
        $periodsArray = [
            Application_Model_Entity_System_SettlementCycleStatus::VERIFIED_STATUS_ID => $this->getCyclePeriods(
                self::VERIFIED_FILTER_TYPE
            ),
            Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID => $this->getCyclePeriods(
                self::PROCESSING_FILTER_TYPE
            ),
        ];

        foreach ($periodsArray as $periodId => $periods) {
            //            foreach ($periods as $id => $value) {
            //                $period = explode(' - ', $value['title']);
            //                if (count($period) == 2) {
            //                    $startDate = new Zend_Date($period[0], 'MM/dd/yyyy');
            //                    $year = $startDate->toString('yyyy');
            //                } else {
            //                    $year = 'not exist';
            //                }
            //                $periodsArray[$year][$id] = $value;
            //            }
            if (empty($periodsArray[$periodId])) {
                $periodsArray[$periodId]['none'] = 'not exist';
            }
        }

        return $periodsArray;
    }

    public function getPeriodOptionsForReport($periodsArray)
    {
        $options = [];
        foreach ($periodsArray as $year => $period) {
            foreach ($period as $id => $value) {
                $options[$year][$id] = $value['title'];
            }
        }

        return $options;
    }

    /**
     * @param int $filterType
     * @param bool $onlyActive
     * @return array
     */
    public function getCyclePeriods($filterType = self::ALL_FILTER_TYPE, $onlyActive = false, $forReports = false)
    {
        $cycleCollections = $this->getCollection()->addFilterByUserRole();
        if ($onlyActive) {
            $cycleCollections->addFilter(
                'status_id',
                Application_Model_Entity_System_SettlementCycleStatus::VERIFIED_STATUS_ID
            );
        }
        $periods = [];
        $this->_cycleCollection = $cycleCollections;
        $cycles = $this->getCyclesFilteredByType($filterType);
        foreach ($cycles as $cycle) {
            $startDate = new Zend_Date($cycle->getCycleStartDate(), 'yyyy-MM-dd');
            $closeDate = new Zend_Date($cycle->getCycleCloseDate(), 'yyyy-MM-dd');
            if ($forReports) {
                $periods[$cycle->getId()]['title'] = $startDate->toString('MM/dd/yy') . ' - ' . $closeDate->toString(
                    'MM/dd/yy'
                );
                $periods[$cycle->getId()]['status'] = $cycle->getStatusId();
            } else {
                $periods[$cycle->getId()] = $startDate->toString('MM/dd/yyyy') . ' - ' . $closeDate->toString(
                    'MM/dd/yyyy'
                );
            }
        }
        if (empty($periods)) {
            if ($forReports) {
                $periods[0]['title'] = 'not exist';
                $periods[0]['status'] = 0;
            } else {
                $periods[0] = 'not exist';
            }
        }

        return $periods;
    }

    public function getCyclePeriodString($short = false)
    {
        $startDate = new Zend_Date($this->getCycleStartDate(), 'yyyy-MM-dd');
        $closeDate = new Zend_Date($this->getCycleCloseDate(), 'yyyy-MM-dd');

        return $startDate->toString($short ? 'MM/dd/yy' : 'MM/dd/yyyy') . ' - ' . $closeDate->toString(
            $short ? 'MM/dd/yy' : 'MM/dd/yyyy'
        );
    }

    /**
     * Return collection of Settlement Cycles, that filtered in accordance with
     * type.
     *
     * @param int $filterType
     * @return array Application_Model_Entity_Collection_Settlement_Cycle
     * @throws Zend_Exception
     */
    public function getCyclesFilteredByType(
        $filterType = self::ALL_FILTER_TYPE
    ) {
        $cycles = [];
        $cycles = match ($filterType) {
            self::CURRENT_FILTER_TYPE => $this->_cycleCollection->addFilter(
                'status_id',
                Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID,
                '!='
            )->setOrder(
                'cycle_close_date',
                Application_Model_Base_Collection::SORT_ORDER_DESC
            )->getItems(),
            self::ALL_FILTER_TYPE => $this->_cycleCollection->setOrder(
                'cycle_close_date',
                Application_Model_Base_Collection::SORT_ORDER_DESC
            )->getItems(),
            self::ALL_FILTER_TYPE_ASC => $this->_cycleCollection->setOrder(
                'cycle_close_date',
                Application_Model_Base_Collection::SORT_ORDER_ASC
            )->getItems(),
            self::ARCHIVE_FILTER_TYPE => $this->_cycleCollection->addFilter(
                'status_id',
                Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID
            )->setOrder('cycle_close_date', Application_Model_Base_Collection::SORT_ORDER_DESC)->getItems(),
            self::LAST_CLOSED_FILTER_TYPE => $this->_cycleCollection->addFilter(
                'status_id',
                Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID
            )->setOrder(
                'cycle_close_date',
                Application_Model_Base_Collection::SORT_ORDER_DESC
            )->getItems(),
            self::ALL_FOR_REPORT_FILTER_TYPE => $this->_cycleCollection->setOrder(
                'cycle_close_date',
                Application_Model_Base_Collection::SORT_ORDER_DESC
            )->addFilter(
                'status_id',
                Application_Model_Entity_System_SettlementCycleStatus::NOT_VERIFIED_STATUS_ID,
                '!='
            )->getItems(),
            self::ALL_FOR_IMPORTING_FILTER_TYPE => $this->_cycleCollection->setOrder(
                'cycle_close_date',
                Application_Model_Base_Collection::SORT_ORDER_DESC
            )->addFilter(
                'status_id',
                [
                    Application_Model_Entity_System_SettlementCycleStatus::VERIFIED_STATUS_ID,
                    Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID,
                ],
                'IN'
            )->getItems(),
            self::VERIFIED_FILTER_TYPE => $this->_cycleCollection->setOrder(
                'cycle_close_date',
                Application_Model_Base_Collection::SORT_ORDER_DESC
            )->addFilter(
                'status_id',
                Application_Model_Entity_System_SettlementCycleStatus::VERIFIED_STATUS_ID
            )->getItems(),
            self::PROCESSING_FILTER_TYPE => $this->_cycleCollection->setOrder(
                'cycle_close_date',
                Application_Model_Base_Collection::SORT_ORDER_DESC
            )->addFilter(
                'status_id',
                Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID
            )->getItems(),
            default => throw new Zend_Exception('Invalid file filter type'),
        };
        if ($cycles == null) {
            $cycles = [];
        }

        return $cycles;
    }

    public function getContractorReserveAccounts()
    {
        $contractorsArray = $this->getCarrier()->getActiveContractors()->getField('entity_id');
        $reserveAccounts = new Application_Model_Entity_Accounts_Reserve();
        $collection = $reserveAccounts->getCollection()->addFilter(
            'entity_id',
            $contractorsArray,
            'IN'
        );

        return $collection;
    }

    public function getBillingCycleOptions()
    {
        $billingCycles = new Application_Model_Entity_System_CyclePeriod();
        $billingCycles = $billingCycles->getBillingCycles(
            $this->getCyclePeriodId()
        );

        return $billingCycles;
    }

    public function updateProcessingDate()
    {
        $cycleCloseDay = new Zend_Date($this->getCycleCloseDate(), Zend_Date::ISO_8601);
        $processingDate = $cycleCloseDay->addDay($this->getPaymentTerms())->toString('yyyy-MM-dd');
        $this->setProcessingDate($processingDate);

        return $this;
    }

    public function updateDisbursementDate()
    {
        $cycleCloseDay = new Zend_Date($this->getCycleCloseDate(), Zend_Date::ISO_8601);
        $disbursementDate = $cycleCloseDay->addDay($this->getDisbursementTerms())->toString('yyyy-MM-dd');
        $this->setDisbursementDate($disbursementDate);

        return $this;
    }

    public function clear()
    {
        $this->checkCurrentStatus(__FUNCTION__);
        $this->getProcessModel()->clear();

        return $this;
    }

    public function reject()
    {
        $this->checkCurrentStatus(__FUNCTION__);
        $this->getProcessModel()->reject();

        return $this;
    }

    /**
     * Trigger settlement cycle data export to Hub
     *
     * @return FlashMessage
     * @throws Exception
     */
    public function export_to_hub(): FlashMessage
    {
        // validate settlement cycle status before proceed
        $this->checkCurrentStatus(__FUNCTION__);

        try {
            $hub = new Hub();
            if ($hub->export_cycle(cycleId: $this->getId())) {
                return new FlashMessage(
                    message: 'Settlement Cycle was successfully exported to Hub.',
                    type: FlashMessage::T_OK
                );
            }
        } catch (Throwable $e) {
            return new FlashMessage(
                message: 'Unable to export Settlement Cycle ID = '. $this->getId() . ' to Hub due to: ' . $e->getMessage(),
                type: FlashMessage::T_ERROR
            );
        }

        return new FlashMessage(
            message: 'Unable to export Settlement Cycle ID = '. $this->getId() . ' to Hub.',
            type: FlashMessage::T_WARNING
        );
    }

    public function getDisbursementStatusTitle()
    {
        if (!$disbursementStatusTitle = $this->getData('disbursement_status_title')) {
            $this->setData(
                'disbursement_status_title',
                (new Application_Model_Entity_System_PaymentStatus())->load($this->getDisbursementStatus())->getTitle()
            );
        }

        return $this->getData('disbursement_status_title');
    }

    /**
     * @return Application_Model_Entity_Settlement_Cycle
     */
    public function getParentCycle()
    {
        $cycle = new self();
        $cycle->load($this->getParentCycleId());

        return $cycle;
    }

    public function hasPreviousNotApprovedCycle()
    {
        $parentCycle = $this->getParentCycle();

        return ($parentCycle->getId() && (int)$parentCycle->getStatusId(
        ) < Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID);
    }

    /**
     * @return bool
     */
    public function hasNextVerifiedCycle()
    {
        $cycle = $this->getCollection()->addFilter('parent_cycle_id', $this->getId())->addFilter(
            'status_id',
            Application_Model_Entity_System_SettlementCycleStatus::NOT_VERIFIED_STATUS_ID,
            '>'
        )->addNonDeletedFilter()->addLimit(1)->getFirstItem();
        if ($cycle->getId()) {
            return true;
        }

        return false;
    }

    public function getNegativeReserveAccounts()
    {
        $historyEntity = new Application_Model_Entity_Accounts_Reserve_History();
        $accounts = $historyEntity->getCollection()->addSettlementFilter($this->getId())->addFilter(
            'current_balance',
            0,
            '<'
        )->addFilter('contractor_vendor_reserve_code', 'CASH', '!=')->addFilter('allow_negative', 0)->getItems();

        return $accounts;
    }

    public function getRule()
    {
        if (!$this->_rule) {
            $this->_rule = (new Application_Model_Entity_Entity_Carrier())->getCycleRule();
        }

        return $this->_rule;
    }

    public function getRuleDateInNextCycle()
    {
        $date = false;
        $ruleStartDate = new Zend_Date($this->getRule()->getCycleStartDate(), Zend_Date::ISO_8601);
        $closeDate = new Zend_Date($this->getCycleCloseDate(), Zend_Date::ISO_8601);
        $periodModel = (new Application_Model_Entity_System_CyclePeriod())->load($this->getCyclePeriodId());
        if ($periodModel->getId()) {
            $nextCycle = clone($this);
            $nextCycle->setCycleStartDate(
                (new Zend_Date($this->getCycleCloseDate(), Zend_Date::ISO_8601))->addDay(1)->toString('yyyy-MM-dd')
            );
            $nextCloseDate = $periodModel->getPeriodLength($nextCycle);
        } else {
            $nextCloseDate = new Zend_Date($closeDate, Zend_Date::ISO_8601);
        }

        if ($ruleStartDate->isLater($closeDate->addDay(1), Zend_Date::DATES) && ($ruleStartDate->equals(
            $nextCloseDate,
            Zend_Date::DATES
        ) || $ruleStartDate->isEarlier($nextCloseDate, Zend_Date::DATES))) {
            $date = $ruleStartDate;
        }

        return $date;
    }

    /**
     * return DatePeriod object for iterations
     *
     * @return DatePeriod
     */
    public function getCycleDatePeriod()
    {
        $begin = new DateTime($this->getCycleStartDate());
        $end = new DateTime($this->getCycleCloseDate());
        $end->modify('+1 day');
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        return $period;
    }

    public function getCycleMonthPeriod($date, $addWeek = false)
    {
        $begin = new DateTime($date);
        $end = clone $begin;
        $end->modify('+1 month');
        if ($addWeek) {
            $begin->modify('+1 week');
        }
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        return $period;
    }

    public function checkPaymentsAndDisbursements()
    {
        $pd = $this->getPaymentsAndDisbursements();

        return (round($pd['payment_amount'], 2) == round($pd['disbursement_amount'], 2));
    }

    public function getPaymentsAndDisbursements()
    {
        //        if (!isset($this->paymentsAndDeductions)) {
        $id = $this->getId();
        $sql = 'CALL getPaymentsAndDisbursements(?)';
        $stmt = $this->getResource()->getAdapter()->prepare($sql);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $this->paymentsAndDeductions = $result[0];
        //        }

        return $this->paymentsAndDeductions;
    }

    public function getVendorsWithNegativeDisbursements()
    {
        $result = false;
        if ($id = $this->getId()) {
            $sql = 'CALL getVendorsWithNegativeDisbursements(?)';
            $stmt = $this->getResource()->getAdapter()->prepare($sql);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            $result = $stmt->fetchAll();
        }

        return $result;
    }

    public function saveHistory()
    {
        $saveHistoryService = new Application_Model_Report_SaveHistoryService($this);
        $saveHistoryService->save();

        return $this;
    }

    public function getCarrierAccountBalances()
    {
        $result = false;
        if ($id = $this->getId()) {
            $sql = 'CALL getCarrierAccountBalances(?)';
            $stmt = $this->getResource()->getAdapter()->prepare($sql);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            $result = $stmt->fetchAll();
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function checkPermissions($checkOnlyAction = false)
    {
        $user = User::getCurrentUser();
        if ($user->isCarrier()) {
            $request = Zend_Controller_Front::getInstance()->getRequest();
            switch ($request->getActionName()) {
                case 'edit':
                    if (!$user->hasPermission('settlement_edit')) {
                        return false;
                    }
                    break;
                case 'verify':
                    if (!$user->hasPermission('settlement_verify')) {
                        return false;
                    }
                    break;
                case 'process':
                    if (!$user->hasPermission('settlement_process')) {
                        return false;
                    }
                    break;
                case 'reject':
                    if (!$user->hasPermission('settlement_reject')) {
                        return false;
                    }
                    break;
                case 'delete':
                    if (!$user->hasPermission('settlement_delete')) {
                        return false;
                    }
                    break;
                case 'approve':
                    if (!$user->hasPermission('settlement_approve')) {
                        return false;
                    }
                    break;
                case 'export':
                    if (!$user->hasPermission('settlement_export')) {
                        return false;
                    }
                    break;
                case 'contractor':
                    if (!$user->hasPermission('settlement_data_view')) {
                        return false;
                    }
                    break;
                default:
                    return false;
            }
        }
        if (!$checkOnlyAction) {
            if ($entityId = $user->getCarrierEntityId()) {
                if ($this->getCarrierId() == $entityId) {
                    return true;
                }
            }

            return false;
        } else {
            return true;
        }
    }

    public function approveDisbursements()
    {
        $db = $this->getResource()->getAdapter();
        $db->beginTransaction();

        try {
            $this->getResource()->update(
                [
                    'disbursement_status' => Application_Model_Entity_System_PaymentStatus::APPROVED_STATUS,
                    'disbursement_approved_datetime' => date('Y-m-d'),
                    'disbursement_approved_by' => User::getCurrentUser()->getId(),
                ],
                ['id = ?' => $this->getId()]
            );

            /** @var DisbursementCollection $disbursementCollection */
            $disbursementCollection = $this->getDisbursement();
            $this->updateDisbursementReference($disbursementCollection);

            $db->commit();
        } catch (Exception) {
            $db->rollBack();
        }
    }

    public function updateDisbursementReference(DisbursementCollection $collection)
    {
        $escrowAccount = $this->getCarrier()->getEscrowAccount();
        $nextCheckNumber = (int)$escrowAccount->getNextCheckNumber() ?: 1;
        $historyId = (int)$escrowAccount->getHistoryId() ?: 0;
        foreach ($collection as $disbursement) {
            $disbursement->getResource()->update(
                [
                    'disbursement_reference' => $nextCheckNumber,
                    'escrow_account_history_id' => $historyId,
                ],
                ['id = ?' => $disbursement->getId()]
            );
            $nextCheckNumber++;
        }
        if ($escrowAccount->getId()) {
            $escrowAccount->getResource()->update(
                ['next_check_number' => $nextCheckNumber],
                ['id = ?' => $escrowAccount->getId()]
            );
            $escrowAccount->load($escrowAccount->getId());
            $escrowAccount->updatePfleetAccount();
        }
    }

    public function getRecurringType()
    {
        return $this->getCyclePeriodId();
    }

    /**
     * @return $this
     */
    public function applyRecurringPayments()
    {
        $payment = new Application_Model_Entity_Payments_Payment();
        $payment->applyRecurrings($this);

        return $this;
    }

    /**
     * @return $this
     */
    public function applyRecurringDeductions()
    {
        $deduction = new Deduction();
        $deduction->applyRecurrings($this);
        // $deduction->reorderImportedPriority($this->getId());

        return $this;
    }

    public function applyHoldDeductions(): self
    {
        (new Deduction())->applyHoldDeductions($this);

        return $this;
    }

    /**
     * @param $date
     * @return bool
     */
    public function inCycle($date)
    {
        $period = $this->getCycleDatePeriod();
        foreach ($period as $day) {
            if ($day->format('Y-m-d') == $date) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Application_Model_Entity_Collection_Settlement_Cycle
     */
    public function getSubsequentCycles()
    {
        $collection = $this->getCollection()->addFilter('carrier_id', $this->getCarrierId())->addFilter(
            'id',
            $this->getId(),
            '>'
        )->addFilter('deleted', 0);

        return $collection;
    }

    public function getFilterType()
    {
        if ($this->getStatusId() != Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID) {
            return Application_Model_Entity_Settlement_Cycle::CURRENT_FILTER_TYPE;
        } else {
            return Application_Model_Entity_Settlement_Cycle::LAST_CLOSED_FILTER_TYPE;
        }
    }

    public function updateReserveAccountHistoryAfterReject()
    {
        $reserveAccounts = $this->getReserveAccountsWithWrongHistory();
        foreach ($reserveAccounts as $account) {
            $account->updateSubsequentCycles($this);
        }

        return $this;
    }

    /**
     * return collection of reserve accounts
     *
     * @return array
     */
    public function getReserveAccountsWithWrongHistory()
    {
        $reserveAccountCollection = [];
        $reserveAccountHistory = new Application_Model_Entity_Accounts_Reserve_History();
        $accounts = $reserveAccountHistory->getResource()->getReserveAccountsWithWrongHistory($this);
        if ($accounts) {
            $reserveAccountCollection = (new Application_Model_Entity_Accounts_Reserve())->getCollection()->addFilter(
                'id',
                $accounts,
                'IN'
            );
        }

        return $reserveAccountCollection;
    }

    /**
     * check for errors and return array with errors
     * return array
     */
    public function getDisbursementErrors()
    {
        $errors = [];
        foreach ($this->getSettlementContractors() as $contractor) {
            if (round($contractor['settlement'], 2) != round($contractor['disbursements'], 2)) {
                $errors[] = 'Disbursement total does not equal settlement total for ' . $contractor['company'];
            }
        }

        if (!$this->checkPaymentsAndDisbursements()) {
            $errors[] = 'The sum of disbursements should equal sum of compensations ($' . number_format(
                $this->getPaymentsAndDisbursements()['payment_amount'],
                2
            ) . ')';
        }

        return $errors;
    }
}
