<?php

use Application_Model_Entity_Settlement_Cycle as Cycle;
use Application_Model_Entity_System_ReserveTransactionTypes as ReserveTransactionTypes;

class Application_Model_Calculations_Settlement
{
    protected $_result;
    /**
     * @var Cycle
     */
    protected $_cycle;
    protected $_payments;
    protected $_rule;

    public function __construct(Cycle $cycle)
    {
        $this->_cycle = $cycle;
    }

    public function process()
    {
        foreach ($this->_cycle->getCarrier()->getContractors() as $contractor) {
            $this->fundingForAllPossible($contractor);
        }

        return $this;
    }

    public function approve()
    {
        if ($this->_cycle->getNegativeReserveAccounts()) {
            throw new Exception(Cycle::CYCLE_STAGE_ERROR);
        }

        $this->_createPaymentDisburstments();
        $this->_createDeductionDisburstments();
    }

    protected function _createPaymentDisburstments()
    {
        $settlement = $this->_cycle->getSettlementContractors();
        $contractor = new Application_Model_Entity_Entity_Contractor();
        foreach ($settlement as $contractorSettlement) {
            if ($contractorSettlement['settlement'] > 0) {
                $contractor->load($contractorSettlement['id'], 'entity_id');
                $settlementAmount = round($contractorSettlement['settlement'], 2);
                $currentAmount = round($contractorSettlement['settlement'], 2);
                if ($currentAmount) {
                    $amount = $currentAmount;
                    if ($amount > 0 || $amount === null) {
                        $disburstment = new Application_Model_Entity_Transactions_Disbursement();
                        $disburstment->setData(
                            [
                                'entity_id' => $contractor->getEntityId(),
                                'process_type' => Application_Model_Entity_System_DisbursementTransactionTypes::PAYMENT_TRANSACTION_TYPE,
                                'amount' => $amount,
                                'status' => Application_Model_Entity_System_PaymentStatus::NOT_APPROVED_STATUS,
                                'created_datetime' => date('Y-m-d H:i:s'),
                                'created_by' => Application_Model_Entity_Accounts_User::SYSTEM_USER,
                                'settlement_cycle_id' => $this->_cycle->getId(),
                            ]
                        );
                        $disburstment->save();
                        $currentAmount = round($currentAmount - $amount, 2);
                    }
                }
            }
            if ($currentAmount > 0) {
                if (isset($disburstment)) {
                    if ($disburstment->getData() != []) {
                        $disburstment->setAmount(
                            $disburstment->getAmount() + $currentAmount
                        );
                        $disburstment->save();
                    }
                }
            }
        }

        return 0;
    }

    protected function _createDeductionDisburstments()
    {
        $settlement = $this->_cycle->getSettlementVendors();
        foreach ($settlement as $vendorSettlement) {
            $entity = new Application_Model_Entity_Entity();
            $entity->load($vendorSettlement['id']);
            $disburstment = new Application_Model_Entity_Transactions_Disbursement();
            $disburstment->setData([
                'entity_id' => $entity->getId(),
                'process_type' => Application_Model_Entity_System_DisbursementTransactionTypes::DEDUCTION_TRANSACTION_TYPE,
                'amount' => $vendorSettlement['amount'],
                'status' => Application_Model_Entity_System_PaymentStatus::NOT_APPROVED_STATUS,
                'created_datetime' => date('Y-m-d H:i:s'),
                'created_by' => Application_Model_Entity_Accounts_User::SYSTEM_USER,
                'settlement_cycle_id' => $this->_cycle->getId(),
            ]);
            $disburstment->save();
        }
    }

    public function verify()
    {
        $this->_createCycle($this->_cycle);
    }

    /**
     * @return Application_Model_Entity_Settlement_Cycle
     */
    protected function _createCycle(Cycle $parentCycle)
    {
        $parentCycleCloseDate = new Zend_Date(
            $parentCycle->getCycleCloseDate(),
            'yyyy-MM-dd'
        );
        $newCycleStartDate = $parentCycleCloseDate->addDay(1);

        $newCycle = clone($parentCycle);
        $newCycle->unsetData($parentCycle->getPrimaryKey())->unsCycleCloseDate()->setParentCycleId(
            $parentCycle->getId()
        );

        $ruleStartDate = new Zend_Date($this->getRule()->getCycleStartDate(), Zend_Date::ISO_8601);
        if ($ruleStartDate->equals($newCycleStartDate, Zend_Date::DATES)) {
            $ruleData = $this->getRule()->getData();
            unset($ruleData['id']);
            $newCycle->addData($ruleData);
        } else {
            $newCycle->setCycleStartDate($newCycleStartDate->toString('yyyy-MM-dd'));
        }
        $newCycle->setPaymentTerms($this->getRule()->getPaymentTerms());
        $newCycle->setDisbursementTerms($this->getRule()->getDisbursementTerms());
        $newCycle->save();

        return $newCycle;
    }

    /**
     * @return Application_Model_Entity_Collection_Payments_Payment
     */
    public function getPayments()
    {
        $paymentsModel = new Application_Model_Entity_Payments_Payment();
        $collection = $paymentsModel->getCollection();
        $collection->addFilter('payments.settlement_cycle_id', $this->_cycle->getId());

        return $collection;
    }

    /**
     * @return Application_Model_Entity_Collection_Deductions_Deduction
     */
    public function getDeductions()
    {
        $deductionsModel = new Application_Model_Entity_Deductions_Deduction();
        $collection = $deductionsModel->getCollection();
        $collection->addFilter('deductions.settlement_cycle_id', $this->_cycle->getId());

        return $collection;
    }

    /**
     * @return Application_Model_Entity_Collection_Accounts_Reserve_Transaction
     */
    public function getReserveTransactions()
    {
        $transactionsModel = new Application_Model_Entity_Accounts_Reserve_Transaction();
        $collection = $transactionsModel->getCollection();
        $collection->addFilter('reserve_transaction.settlement_cycle_id', $this->_cycle->getId());

        return $collection;
    }

    /**
     * @return Application_Model_Entity_Collection_Accounts_Reserve_Transaction
     */
    public function getContributions()
    {
        $collection = $this->getReserveTransactions();
        $collection->addFilter(
            'reserve_transaction.type',
            ReserveTransactionTypes::CONTRIBUTION
        );

        return $collection;
    }

    /**
     * @return Application_Model_Entity_Collection_Accounts_Reserve_Transaction
     */
    public function getWithdrawals()
    {
        $collection = $this->getReserveTransactions();
        $collection->addFilter(
            'reserve_transaction.type',
            ReserveTransactionTypes::WITHDRAWAL
        );

        return $collection;
    }

    /**
     * @TODO rewrite (don't delete)
     * returns the sum of all the payments for current contractor
     * @return float
     */
    public function getPaymentsSum(Application_Model_Entity_Entity_Contractor $contractor)
    {
        $paymentsSum = 0;
        $this->_payments = $this->getPayments()->addFilter(
            'payments.contractor_id',
            $contractor->getEntityId()
        )->addFilter('payments.deleted', 0);

        foreach ($this->_payments as $payment) {
            $paymentsSum += $payment->getBalance();
        }

        return $paymentsSum;
    }

    public function fundingForAllPossible($contractor): void
    {
        $transactionEntity = new Application_Model_Entity_Accounts_Reserve_Transaction();
        $paymentsSum = $this->getPaymentsSum($contractor);
        $deductions = $this->getDeductions()->addFilter(
            'deductions.contractor_id',
            $contractor->getEntityId()
        )->addApprovedVendorFilter($contractor)->addNonDeletedFilter(
            // )->setOrder(
            //     'priority',
            //     Application_Model_Base_Collection::SORT_ORDER_ASC
        )->getItems();

        /**
         * @var Application_Model_Entity_Deductions_Deduction $deduction
         */
        foreach ($deductions as $deduction) {
            if ($paymentsSum >= $deduction->getBalance()) {
                $paymentsSum -= $deduction->getBalance();
                $deduction->setBalance(0);
                $deduction->save();
            } else {
                //$transSum = $deduction->getBalance() - $paymentsSum;
                $transFundSum = 0;
                /*if ($deduction->getEligible() && $deduction->getReserveAccountReceiver()) {
                    $transaction = $transactionEntity->create(
                        $deduction->getReserveAccountReceiver(),
                        $contractor,
                        $deduction->getSettlementCycleId(),
                        ReserveTransactionTypes::WITHDRAWAL,
                        $transSum,
                        false,
                        true,
                        $deduction->getId()
                    );
                    if ($transaction) {
                        $transFundSum = $transaction->getAmount();
                    }
                }*/
                $deduction->setBalance($deduction->getBalance() - $paymentsSum - $transFundSum);
                $deduction->save();
                $paymentsSum = 0;
            }
        }
        if ($paymentsSum) {
            $contractor->updateReserveAccount($this->_cycle, $paymentsSum);
        }

        // $transactionEntity->reorderImportedPriority($this->_cycle->getId());
    }

    /**
     * 1) mark all cycle  payments, deductions and transactions as deleted
     * 2) set not verified status for settlement cycle
     * 3) delete next cycle
     *
     * @return $this
     * @throws Exception
     */
    public function clear()
    {
        $db = $this->_cycle->getResource()->getAdapter();
        $db->beginTransaction();

        try {
            $payment = new Application_Model_Entity_Payments_Payment();
            $payment->clear($this->_cycle);

            $deduction = new Application_Model_Entity_Deductions_Deduction();
            $deduction->clear($this->_cycle);

            $transactions = new Application_Model_Entity_Accounts_Reserve_Transaction();
            $transactions->getResource()->deleteCycleTransactions($this->_cycle->getId());

            $this->_cycle->updateReserveAccountContractorAfterClear();
            $this->_cycle->updateReserveAccountVendorProcess();

            $this->_cycle->getResource()->changeStatusId(
                $this->_cycle->getId(),
                Application_Model_Entity_System_SettlementCycleStatus::NOT_VERIFIED_STATUS_ID
            );

            $this->_cycle->getResource()->deleteParentCycle($this->_cycle->getId());

            $db->commit();
            Application_Model_Entity_Accounts_User::getCurrentUser()->reloadCycle();
        } catch (Exception $e) {
            $db->rollBack();

            throw $e;
        }

        return $this;
    }

    public function reject()
    {
        $db = $this->_cycle->getResource()->getAdapter();
        $db->beginTransaction();

        try {
            $transactions = new Application_Model_Entity_Accounts_Reserve_Transaction();
            $transactions->getResource()->deleteWithdrawals($this->_cycle->getId());
            $transactions->getResource()->deleteContributions($this->_cycle->getId());

            $payment = new Application_Model_Entity_Payments_Payment();
            $payment->getResource()->resetPayments($this->_cycle->getId());

            $deduction = new Application_Model_Entity_Deductions_Deduction();
            $deduction->getResource()->resetDeductions($this->_cycle->getId());

            $this->_cycle->updateReserveAccountContractorProcess();
            $this->_cycle->updateReserveAccountVendorProcess();

            $this->_cycle->getResource()->changeStatusId(
                $this->_cycle->getId(),
                Application_Model_Entity_System_SettlementCycleStatus::VERIFIED_STATUS_ID
            );

            $db->commit();

            //update reserve account history
            $this->_cycle->updateReserveAccountHistoryAfterReject();

            Application_Model_Entity_Accounts_User::getCurrentUser()->reloadCycle();
        } catch (Exception $e) {
            $db->rollBack();

            throw $e;
        }

        return $this;
    }

    public function getRule()
    {
        if (!$this->_rule) {
            $this->_rule = (new Application_Model_Entity_Entity_Carrier())->getCycleRule();
        }

        return $this->_rule;
    }
}
