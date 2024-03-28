<?php

class SettlementTest extends BaseTestCase
{
    private static ?array $array = null;

    public function testBeforeTestCreateModel()
    {
        Application_Model_Entity_Accounts_User::login(16);
        $carrier = (new Application_Model_Entity_Entity_Carrier())->getCollection()
            ->getFirstItem();
        $vendor = (new Application_Model_Entity_Entity_Vendor())->getCollection()
            ->getFirstItem();
        $contractor = (new Application_Model_Entity_Entity_Contractor())->getCollection()
            ->getFirstItem();
        $cycle = (new Application_Model_Entity_Settlement_Cycle())->setData(
            [
                'carrier_id' => $carrier->getEntityId(),
                'cycle_period_id' => '1',    //weekly
                'cycle_start_date' => '2014-01-01',
                'cycle_close_date' => '2014-01-07',
                'disbursement_terms' => '1',
                'payment_terms' => '1',
                'status_id' => '1',
            ]
        )
            ->save();

        $paymentSetup = (new Application_Model_Entity_Payments_Setup())->getCollection()
            ->getFirstItem();
        $payment = new Application_Model_Entity_Payments_Payment();
        $payment->setData(
            [
                'setup_id' => $paymentSetup->getId(),
                'contractor_id' => $contractor->getEntity()
                    ->getId(),
                'carrier_id' => $carrier->getEntityId(),
                'rate' => '1000',
                'quantity' => '1',
                'recurring' => '0',
                'terms' => '1',
                'settlement_cycle_id' => $cycle->getId(),
                'billing_cycle_id' => '1',
            ]
        )
            ->save();

        $deductionSetup = (new Application_Model_Entity_Deductions_Setup())->getCollection()
            ->getFirstItem();
        $deduction = new Application_Model_Entity_Deductions_Deduction();
        $deduction->setData(
            [
                'setup_id' => $deductionSetup->getId(),
                'contractor_id' => $contractor->getEntityId(),
                'rate' => '800',
                'quantity' => '1',
                'recurring' => '0',
                'terms' => '1',
                'settlement_cycle_id' => $cycle->getId(),
                'billing_cycle_id' => '1',   //weekly
                'provider_id' => $carrier->getEntityId(),
            ]
        )
            ->save();

        $reserveAccountVendor = (new Application_Model_Entity_Accounts_Reserve_Vendor())->setData(
            [
                'entity_id' => $vendor->getEntityId(),
                'priority' => '1',
                'min_balance' => '100',
                'current_balance' => '2000',
                'contribution_amount' => '50',
                'initial_balance' => '0',
                'description' => 'Model_SettlementTest_VendorRA',
            ]
        )
            ->save();

        $reserveAccountContractorVendor = (new Application_Model_Entity_Accounts_Reserve_Contractor())->setData(
            [
                'entity_id' => $contractor->getEntityId(),
                'priority' => '1',
                'min_balance' => '100',
                'current_balance' => '2000',
                'contribution_amount' => '50',
                'initial_balance' => '0',
                'description' => 'Model_SettlementTest_ContractorRA',
                'reserve_account_vendor_id' => $reserveAccountVendor->getId(),
            ]
        )
            ->save();

        $contribution = (new Application_Model_Entity_Accounts_Reserve_Transaction())->setData(
            [
                'reserve_account_contractor' => $reserveAccountContractorVendor->getReserveAccountId(),
                'reserve_account_vendor' => $reserveAccountVendor->getReserveAccountId(),
                'type' => Application_Model_Entity_System_ReserveTransactionTypes::CONTRIBUTION,
                'settlement_cycle_id' => $cycle->getId(),
                'status' => Application_Model_Entity_System_PaymentStatus::PROCESSED_STATUS,
                'contractor_id' => $contractor->getEntityId(),
                'adjusted_balance' => '48',
                'amount' => '100',
                'description' => 'Model_SettlementTest',
            ]
        )
            ->save();

        $withdrawal = (new Application_Model_Entity_Accounts_Reserve_Transaction())->setData(
            [
                'reserve_account_contractor' => $reserveAccountContractorVendor->getReserveAccountId(),
                'reserve_account_vendor' => $reserveAccountVendor->getReserveAccountId(),
                'type' => Application_Model_Entity_System_ReserveTransactionTypes::WITHDRAWAL,
                'settlement_cycle_id' => $cycle->getId(),
                'status' => Application_Model_Entity_System_PaymentStatus::PROCESSED_STATUS,
                'contractor_id' => $contractor->getEntityId(),
                'adjusted_balance' => '44',
                'amount' => '200',
                'description' => 'Model_SettlementTest',
            ]
        )
            ->save();

        self::$array = [
            'cycle' => $cycle->getId(),
            'carrier' => $carrier->getId(),
            'vendor' => $vendor->getId(),
            'contractor' => $contractor->getId(),
            'paymentSetup' => $paymentSetup->getId(),
            'payment' => $payment->getId(),
            'deductionSetup' => $deductionSetup->getId(),
            'deduction' => $deduction->getId(),
            'reserveAccountVendor' => $reserveAccountVendor->getId(),
            'reserveAccountContractorVendor' => $reserveAccountContractorVendor->getId(),
            'contribution' => $contribution->getId(),
            'withdrawal' => $withdrawal->getId(),
        ];
    }

    //    public function testGetContributionsSum()
    //    {
    //        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
    //        $contractor = (new Application_Model_Entity_Entity_Contractor())->load(self::$array['contractor']);
    //        $calculation = new Application_Model_Calculations_Settlement($cycle);
    //        $calculation->getContributionsSum($contractor);//100
    //    }

    //    public function testGetContributionsAdjustedSum()
    //    {
    //        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
    //        $contractor = (new Application_Model_Entity_Entity_Contractor())->load(self::$array['contractor']);
    //        $calculation = new Application_Model_Calculations_Settlement($cycle);
    //        $calculation->getContributionsAdjustedSum($contractor);//200
    //    }

    //    public function testGetTransactionSumWithSign()
    //    {
    //        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
    //        $contractor = (new Application_Model_Entity_Entity_Contractor())->load(self::$array['contractor']);
    //        $calculation = new Application_Model_Calculations_Settlement($cycle);
    //        $calculation->getTransactionSumWithSign($contractor);
    //    }

    //    public function testGetAccCurrentBalanceSum()
    //    {
    //        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
    //        $contractor = (new Application_Model_Entity_Entity_Contractor())->load(self::$array['contractor']);
    //        $calculation = new Application_Model_Calculations_Settlement($cycle);
    //        $calculation->getAccCurrentBalanceSum($contractor);
    //    }

    public function testGetPaymentsSum()
    {
        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
        $contractor = (new Application_Model_Entity_Entity_Contractor())->load(self::$array['contractor']);
        $calculation = new Application_Model_Calculations_Settlement($cycle);
        $calculation->getPaymentsSum($contractor);
    }

    //    public function testGetPaymentsAmountSum()
    //    {
    //        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
    //        $contractor = (new Application_Model_Entity_Entity_Contractor())->load(self::$array['contractor']);
    //        $calculation = new Application_Model_Calculations_Settlement($cycle);
    //        $calculation->getPaymentsAmountSum($contractor);
    //    }

    //    public function testGetDeductionsAdjustedSum()
    //    {
    //        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
    //        $contractor = (new Application_Model_Entity_Entity_Contractor())->load(self::$array['contractor']);
    //        $calculation = new Application_Model_Calculations_Settlement($cycle);
    //        $calculation->getDeductionsAdjustedSum($contractor);
    //    }

    //    public function testGetDeductionsSum()
    //    {
    //        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
    //        $contractor = (new Application_Model_Entity_Entity_Contractor())->load(self::$array['contractor']);
    //        $calculation = new Application_Model_Calculations_Settlement($cycle);
    //        $calculation->getDeductionsSum($contractor);
    //    }

    //    public function testGetDeductionsAmountSum()
    //    {
    //        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
    //        $contractor = (new Application_Model_Entity_Entity_Contractor())->load(self::$array['contractor']);
    //        $calculation = new Application_Model_Calculations_Settlement($cycle);
    //        $calculation->getDeductionsAmountSum($contractor);
    //    }

    public function testFundingForAllPossible()
    {
        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
        $contractor = (new Application_Model_Entity_Entity_Contractor())->load(self::$array['contractor']);
        $calculation = new Application_Model_Calculations_Settlement($cycle);
        $calculation->fundingForAllPossible($contractor);
    }

    //    public function testGetSettlementSum()
    //    {
    //        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
    //        $calculation = new Application_Model_Calculations_Settlement($cycle);
    //        $calculation->getSettlementSum();
    //    }

    public function testVerify()
    {
        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
        $calculation = new Application_Model_Calculations_Settlement($cycle);
        $calculation->verify();
    }

    public function testProcess()
    {
        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
        $calculation = new Application_Model_Calculations_Settlement($cycle);
        $calculation->process();
    }

    public function testApprove()
    {
        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
        $calculation = new Application_Model_Calculations_Settlement($cycle);
        $calculation->approve();
    }

    //    public function testHasNotApprovedPayments()
    //    {
    //        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
    //        $calculation = new Application_Model_Calculations_Settlement($cycle);
    //        $calculation->hasNotApprovedPayments();
    //    }
    //
    //    public function testHasNotApprovedDeductions()
    //    {
    //        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
    //        $calculation = new Application_Model_Calculations_Settlement($cycle);
    //        $calculation->hasNotApprovedDeductions();
    //    }

    //    public function testHasNotApprovedReserveTransaction()
    //    {
    //        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
    //        $calculation = new Application_Model_Calculations_Settlement($cycle);
    //        $calculation->hasNotApprovedReserveTransaction();
    //    }

    public function testClear()
    {
        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
        $calculation = new Application_Model_Calculations_Settlement($cycle);
        $calculation->clear();
    }

    /**
     * @expectedException Exception
     */
    public function testClearException()
    {
        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
        $entity = new Application_Model_Calculations_Settlement($cycle);
        $entity->clear();
    }

    public function testReject()
    {
        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
        $calculation = new Application_Model_Calculations_Settlement($cycle);
        $calculation->reject();
    }

    /**
     * @expectedException Exception
     */
    public function testRejectException()
    {
        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
        $entity = new Application_Model_Calculations_Settlement($cycle);
        $entity->reject();
    }

    //    public function testGetResult()
    //    {
    //        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
    //        $calculation = new Application_Model_Calculations_Settlement($cycle);
    //        $calculation->getResult();
    //    }
    //
    //    public function testIsFullyFundedCycle()
    //    {
    //        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
    //        $calculation = new Application_Model_Calculations_Settlement($cycle);
    //        $calculation->isFullyFundedCycle();
    //    }
    //
    //    public function testGetWithdrawalSum()
    //    {
    //        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
    //        $contractor = (new Application_Model_Entity_Entity_Contractor())->load(self::$array['contractor']);
    //        $calculation = new Application_Model_Calculations_Settlement($cycle);
    //        $calculation->getWithdrawalSum($contractor);
    //    }
    //
    //    public function testIsFullyFundedContractor()
    //    {
    //        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
    //        $contractor = (new Application_Model_Entity_Entity_Contractor())->load(self::$array['contractor']);
    //        $calculation = new Application_Model_Calculations_Settlement($cycle);
    //        $calculation->isFullyFundedContractor($contractor);
    //    }
    //
    //    public function testSetTotals()
    //    {
    //        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
    //        $contractor = (new Application_Model_Entity_Entity_Contractor())->load(self::$array['contractor']);
    //        $calculation = new Application_Model_Calculations_Settlement($cycle);
    //        $calculation->setTotals($contractor);
    //    }
    //    public function testGetReserveTransactionsSum()
    //    {
    //        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
    //        $contractor = (new Application_Model_Entity_Entity_Contractor())->load(self::$array['contractor']);
    //        $calculation = new Application_Model_Calculations_Settlement($cycle);
    //        $calculation->getReserveTransactionsSum($contractor);
    //    }
    //
    //    public function testGetReserveTransactionsAdjustedSum()
    //    {
    //        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load(self::$array['cycle']);
    //        $contractor = (new Application_Model_Entity_Entity_Contractor())->load(self::$array['contractor']);
    //        $calculation = new Application_Model_Calculations_Settlement($cycle);
    //        $calculation->getReserveTransactionsAdjustedSum($contractor);
    //    }

}
