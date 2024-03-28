<?php

class CalculationTest extends BaseTestCase
{

    public function testSettlementSinglePayment()
    {
        $carrier = $this->newCarrier();
        $contractor = $this->newContractor($carrier, [], false);
        $contractorBA = $this->newBankAccount($contractor);
        $paymentSetup = $this->newPaymentSetup(
            $carrier,
            [
                'rate' => '99.72',
            ]
        );
        $cycle = $this->newCycle(
            $carrier,
            [
                'cycle_period_id' => Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID,
                'cycle_start_date' => '2014-06-01',
                'cycle_close_date' => '2014-06-08',
            ]
        );
        Application_Model_Entity_Accounts_User::login($this->_myUser);

        $cycle->verify();
        $payment = $this->newPayment($contractor, $paymentSetup, $cycle);

        $childCycle = $this->collectionToArray(
            (new Application_Model_Entity_Settlement_Cycle())->getCollection()
                ->addFilter('parent_cycle_id', $cycle->getId())
                ->getItems()
        );
        $this->assertEquals(count($childCycle), 1);
        $this->assertEquals(
            $childCycle[0]['status_id'],
            Application_Model_Entity_System_SettlementCycleStatus::NOT_VERIFIED_STATUS_ID
        );

        $cycle->process();
        $cycle->approve();

        $analytic = $this->getCycleActions($cycle);

        $this->assertEquals(
            $analytic['cycle']['status_id'],
            Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID
        );

        $this->assertEquals(
            [
                '1',
                '0',
                '0',
                '1',
            ],
            [
                is_countable($analytic['payments']) ? count($analytic['payments']) : 0,
                is_countable($analytic['deductions']) ? count($analytic['deductions']) : 0,
                is_countable($analytic['transactions']) ? count($analytic['transactions']) : 0,
                is_countable($analytic['disbursements']) ? count($analytic['disbursements']) : 0,
            ]
        );

        $this->assertEquals($analytic['payments'][0]['amount'], '99.72');
        $this->assertEquals($analytic['payments'][0]['contractor_id'], $contractor->getData('entity_id'));

        $this->assertEquals($analytic['disbursements'][0]['amount'], '99.72');
        $this->assertEquals($analytic['disbursements'][0]['entity_id'], $contractor->getData('entity_id'));
        $this->assertEquals($analytic['disbursements'][0]['disbursement_date'], '2014-06-08');
        $this->assertEquals(
            $analytic['disbursements'][0]['process_type'],
            Application_Model_Entity_System_DisbursementTransactionTypes::PAYMENT_TRANSACTION_TYPE
        );
        $account = (new Application_Model_Entity_Accounts_Bank_History())->load(
            $analytic['disbursements'][0]['bank_account_history_id']
        );
        $this->assertEquals($account->getData('bank_account_id'), $contractorBA->getId());
    }

    public function testSettlementSingleDeduction()
    {
        $carrier = $this->newCarrier();
        $contractor = $this->newContractor($carrier);
        $deductionSetup = $this->newDeductionSetup(
            $carrier,
            [
                'rate' => '5.12',
            ]
        );
        $cycle = $this->newCycle(
            $carrier,
            [
                'cycle_period_id' => Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID,
                'cycle_start_date' => '2014-06-01',
                'cycle_close_date' => '2014-06-08',
            ]
        );

        Application_Model_Entity_Accounts_User::login($this->_myUser);
        $cycle->verify();
        $deduction = $this->newDeduction($contractor, $deductionSetup, $cycle);

        $childCycle = $this->collectionToArray(
            (new Application_Model_Entity_Settlement_Cycle())->getCollection()
                ->addFilter('parent_cycle_id', $cycle->getId())
                ->getItems()
        );
        $this->assertEquals(count($childCycle), 1);
        $this->assertEquals(
            $childCycle[0]['status_id'],
            Application_Model_Entity_System_SettlementCycleStatus::NOT_VERIFIED_STATUS_ID
        );

        $cycle->process();
        $analytic = $this->getCycleActions($cycle);

        $this->assertEquals(
            $analytic['cycle']['status_id'],
            Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID
        );

        $this->assertEquals(
            [
                '0',
                '1',
                '0',
                '0',
            ],
            [
                is_countable($analytic['payments']) ? count($analytic['payments']) : 0,
                is_countable($analytic['deductions']) ? count($analytic['deductions']) : 0,
                is_countable($analytic['transactions']) ? count($analytic['transactions']) : 0,
                is_countable($analytic['disbursements']) ? count($analytic['disbursements']) : 0,
            ]
        );

        $this->assertEquals($analytic['deductions'][0]['amount'], '5.12');
        $this->assertEquals($analytic['deductions'][0]['contractor_id'], $contractor->getData('entity_id'));
        $this->assertEquals($analytic['deductions'][0]['provider_id'], $carrier->getData('entity_id'));

        //$this->setExpectedException('Exception','Cycle stage error');
        //$cycle->approve();
    }

    public function testSettlementNoRAs()
    {
        /* Test Model #1
         * Carrier (1) (BAId#1001)
         *      Contractor (1) (BA1: 100%) (BAId#1003)
         * Compensation = 1000$
         * Deduction = 800$
         *
         * after process
         *  Asserts:
         *          No reserve transactions
         *          Deduction Balance = 0$
         * after approve
         *  Asserts:
         *          Has 2 disbursement transactions
         *          Carrier transaction amount = 800$
         *          Contractor transaction amount = 1000$ - 800$ = 200$
         */

        $carrier = $this->newCarrier([], false);
        $carrierBA = $this->newBankAccount($carrier);

        $contractor = $this->newContractor($carrier, [], false);
        $contractorBA = $this->newBankAccount($contractor);

        $paymentSetup = $this->newPaymentSetup(
            $carrier,
            [
                'quantity' => '10',
                'rate' => '100',
            ]
        );
        $deductionSetup = $this->newDeductionSetup(
            $carrier,
            [
                'quantity' => '80',
                'rate' => '10',
            ]
        );
        $cycle = $this->newCycle(
            $carrier,
            [
                'cycle_period_id' => Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID,
                'cycle_start_date' => '2014-06-01',
                'cycle_close_date' => '2014-06-08',
            ]
        );

        Application_Model_Entity_Accounts_User::login($this->_myUser);
        $cycle->verify();
        $payment = $this->newPayment($contractor, $paymentSetup, $cycle);
        $deduction = $this->newDeduction($contractor, $deductionSetup, $cycle);

        $cycle->process();
        $cycle->approve();
        $analytic = $this->getCycleActions($cycle);

        $this->assertEquals(
            $analytic['cycle']['status_id'],
            Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID
        );

        $this->assertEquals(
            [
                '1',
                '1',
                '0',
                '2',
            ],
            [
                is_countable($analytic['payments']) ? count($analytic['payments']) : 0,
                is_countable($analytic['deductions']) ? count($analytic['deductions']) : 0,
                is_countable($analytic['transactions']) ? count($analytic['transactions']) : 0,
                is_countable($analytic['disbursements']) ? count($analytic['disbursements']) : 0,
            ]
        );

        $this->assertEquals($analytic['payments'][0]['amount'], '1000');
        $this->assertEquals($analytic['payments'][0]['contractor_id'], $contractor->getData('entity_id'));
        $this->assertEquals($analytic['payments'][0]['carrier_id'], $carrier->getData('entity_id'));

        $this->assertEquals($analytic['deductions'][0]['amount'], '800');
        $this->assertEquals($analytic['deductions'][0]['contractor_id'], $contractor->getData('entity_id'));
        $this->assertEquals($analytic['deductions'][0]['provider_id'], $carrier->getData('entity_id'));

        $this->assertEquals($analytic['disbursements'][0]['amount'], '200');
        $this->assertEquals($analytic['disbursements'][0]['entity_id'], $contractor->getData('entity_id'));
        $this->assertEquals(
            $analytic['disbursements'][0]['process_type'],
            Application_Model_Entity_System_DisbursementTransactionTypes::PAYMENT_TRANSACTION_TYPE
        );
        $account = (new Application_Model_Entity_Accounts_Bank_History())->load(
            $analytic['disbursements'][0]['bank_account_history_id']
        );
        $this->assertEquals($account->getData('bank_account_id'), $contractorBA->getId());

        $this->assertEquals($analytic['disbursements'][1]['amount'], '800');
        $this->assertEquals($analytic['disbursements'][1]['entity_id'], $carrier->getData('entity_id'));
        $this->assertEquals(
            $analytic['disbursements'][1]['process_type'],
            Application_Model_Entity_System_DisbursementTransactionTypes::DEDUCTION_TRANSACTION_TYPE
        );
        $account = (new Application_Model_Entity_Accounts_Bank_History())->load(
            $analytic['disbursements'][1]['bank_account_history_id']
        );
        $this->assertEquals($account->getData('bank_account_id'), $carrierBA->getId());
    }

    public function testPaymentMoreDeductionRAContribution()
    {
        $this->markTestSkipped(
            'testPaymentMoreDeductionRAContribution should be fixed!'
        );
        /* Test Model #3
         * Carrier (1)
         *      Contractor (1) (BA1: 100%)
         *          Reserve #1   MinBalance = 500$, CurrentBalance = 300$, ContributionAmount = 100$, Priority = 1; Entity: Vendor1
         *          Reserve #2   MinBalance = 500$, CurrentBalance = 300$, ContributionAmount = 125$, Priority = 2; Entity: Vendor2
         * Compensation   = 325$
         * Deduction = 200$
         * after process
         *          Deduction Balance = 0$
         *          Has Only Reserve Contributions
         *          Reserve Contributions count = 2
         *          Reserve Contribution to #1 = 100$
         *          Reserve Contribution to #2 = 25$
         * after approve
         *          Has 3 disbursement transaction
         *          Carrier disbursement transaction amount = 200$ (D{200}+C{0}  -W{0} = 200)
         *          Vendor disbursement transaction amount = 100$  (D{0}  +C{100}-W{0} = 100)
         *          Vendor disbursement transaction amount =  25$  (D{0}  +C{25} -W{0} = 25)
         *          Contractor has no disbursement transactions (Settlement = P{325} - D{200} - C{125} + W{0} = 0)
         */

        $carrier = $this->newCarrier([], false);
        $carrierBA = $this->newBankAccount($carrier);
        $carrierRA = $this->newReserveAccount($carrier);

        $vendor = $this->newVendor($carrier, [], false);
        $vendorBA = $this->newBankAccount($vendor);
        $vendorRA = $this->newReserveAccount($vendor);

        $contractor = $this->newContractor($carrier, [], false);
        $contractorBA = $this->newBankAccount($contractor);

        $contractorRA1 = $this->newReserveAccount(
            $contractor,
            [
                'priority' => '0',
                'min_balance' => '500',
                'contribution_amount' => '100',
                'initial_balance' => '300',
                'current_balance' => '300',
                'balance' => '300',
                'starting_balance' => '300',
                'verify_balance' => '300',
                'reserve_account_vendor_id' => $carrierRA->getId(),
            ]
        );

        $contractorRA2 = $this->newReserveAccount(
            $contractor,
            [
                'priority' => '1',
                'min_balance' => '500',
                'contribution_amount' => '125',
                'initial_balance' => '300',
                'current_balance' => '300',
                'balance' => '300',
                'starting_balance' => '300',
                'verify_balance' => '300',
                'reserve_account_vendor_id' => $vendorRA->getId(),
            ]
        );

        $paymentSetup = $this->newPaymentSetup(
            $carrier,
            [
                'rate' => '325',
            ]
        );
        $deductionSetup = $this->newDeductionSetup(
            $carrier,
            [
                'rate' => '200',
            ]
        );
        $cycle = $this->newCycle(
            $carrier,
            [
                'cycle_period_id' => Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID,
                'cycle_start_date' => '2014-06-01',
                'cycle_close_date' => '2014-06-08',
            ]
        );

        Application_Model_Entity_Accounts_User::login($this->_myUser);
        $cycle->verify();
        $payment = $this->newPayment($contractor, $paymentSetup, $cycle);
        $deduction = $this->newDeduction($contractor, $deductionSetup, $cycle);

        $cycle->process();
        $cycle->approve();
        $analytic = $this->getCycleActions($cycle);

        $this->assertEquals(
            $analytic['cycle']['status_id'],
            Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID
        );

        //        $this->assertEquals(
        //            array(
        //                '1','1','2','2'
        //            ),
        //            array(
        //                count($analytic['payments']),
        //                count($analytic['deductions']),
        //                count($analytic['transactions']),
        //                count($analytic['disbursements']),
        //            )
        //        );

        $this->assertEquals($analytic['payments'][0]['amount'], '325');
        $this->assertEquals($analytic['payments'][0]['contractor_id'], $contractor->getData('entity_id'));
        $this->assertEquals($analytic['payments'][0]['carrier_id'], $carrier->getData('entity_id'));

        $this->assertEquals($analytic['deductions'][0]['amount'], '200');
        $this->assertEquals($analytic['deductions'][0]['contractor_id'], $contractor->getData('entity_id'));
        $this->assertEquals($analytic['deductions'][0]['provider_id'], $carrier->getData('entity_id'));

        $this->assertEquals($analytic['transactions'][0]['amount'], '100');
        $this->assertEquals(
            $analytic['transactions'][0]['type'],
            Application_Model_Entity_System_ReserveTransactionTypes::CONTRIBUTION
        );
        $this->assertEquals(
            $analytic['transactions'][0]['reserve_account_contractor'],
            $contractorRA1->getData('reserve_account_id')
        );
        $this->assertEquals(
            $analytic['transactions'][0]['reserve_account_vendor'],
            $carrierRA->getData('reserve_account_id')
        );

        $this->assertEquals($analytic['transactions'][1]['amount'], '25');
        $this->assertEquals(
            $analytic['transactions'][1]['type'],
            Application_Model_Entity_System_ReserveTransactionTypes::CONTRIBUTION
        );
        $this->assertEquals(
            $analytic['transactions'][1]['reserve_account_contractor'],
            $contractorRA2->getData('reserve_account_id')
        );
        $this->assertEquals(
            $analytic['transactions'][1]['reserve_account_vendor'],
            $vendorRA->getData('reserve_account_id')
        );

        $this->assertEquals(
            $analytic['disbursements'][0]['process_type'],
            Application_Model_Entity_System_DisbursementTransactionTypes::DEDUCTION_TRANSACTION_TYPE
        );
        $account = (new Application_Model_Entity_Accounts_Bank_History())->load(
            $analytic['disbursements'][0]['bank_account_history_id']
        );
        $this->assertEquals($account->getData('bank_account_id'), $carrierBA->getId());
        $this->assertEquals($analytic['disbursements'][0]['amount'], '300');
        $this->assertEquals($analytic['disbursements'][0]['entity_id'], $carrier->getData('entity_id'));

        $this->assertEquals(
            $analytic['disbursements'][1]['process_type'],
            Application_Model_Entity_System_DisbursementTransactionTypes::DEDUCTION_TRANSACTION_TYPE
        );
        $account = (new Application_Model_Entity_Accounts_Bank_History())->load(
            $analytic['disbursements'][1]['bank_account_history_id']
        );
        $this->assertEquals($account->getData('bank_account_id'), $vendorBA->getId());
        $this->assertEquals($analytic['disbursements'][1]['amount'], '25');
        $this->assertEquals($analytic['disbursements'][1]['entity_id'], $vendor->getData('entity_id'));
    }

    public function testPaymentMoreDeductionRAContributionContractorBankAccounts()
    {
        //* Test Model #4
        //* Carrier (1)
        //*      Contractor (1)
        //*             BA1      Limit: $500    Priority: 1
        //*             BA2      Limit: $300    Priority: 2
        //*             BA3      Limit:  50%    Priority: 3
        //*             BA4      Limit:  50%    Priority: 4
        //*
        //* Compensation   = 1300.12$
        //* Deduction = 200.12$
        //* after approve
        //* Reserve transactions:
        //*      Contribution 100$
        //*  Contractor has 4 disbursement transactions:
        //*      BA1   Funds:   500  Calculate: 500 {$500}               Amount: 500
        //*      BA2   Funds:   300  Calculate: 300 {$300}               Amount: 300
        //*      BA3   Funds:   200  Calculate: 300 {$1000*0.3=$300}     Amount: 200
        //*      BA4   Funds:     0  ---
        //*  Carrier has 1 disbursement transaction:
        //*      BA    Founds:  300.12 Calculate: 1300.12-200.12

        $carrier = $this->newCarrier([], false);
        $carrierBA = $this->newBankAccount($carrier);
        $carrierRA = $this->newReserveAccount($carrier);

        $contractor = $this->newContractor($carrier, [], false);
        $contractorBA1 = $this->newBankAccount(
            $contractor,
            [
                'limit_type' => 0,
                'priorty' => '0',
                'percentage' => '',
                'amount' => '500',
            ]
        );
        $contractorBA2 = $this->newBankAccount(
            $contractor,
            [
                'limit_type' => 0,
                'priorty' => '1',
                'percentage' => '',
                'amount' => '300',
            ]
        );
        $contractorBA3 = $this->newBankAccount(
            $contractor,
            [
                'limit_type' => 0,
                'priorty' => '2',
                'percentage' => '50',
                'amount' => '',
            ]
        );
        $contractorBA4 = $this->newBankAccount(
            $contractor,
            [
                'limit_type' => 0,
                'priorty' => '3',
                'percentage' => '50',
                'amount' => '',
            ]
        );

        $contractorRA = $this->newReserveAccount(
            $contractor,
            [
                'priority' => '0',
                'min_balance' => '500',
                'contribution_amount' => '100',
                'initial_balance' => '300',
                'current_balance' => '300',
                'balance' => '300',
                'starting_balance' => '300',
                'verify_balance' => '300',
                'reserve_account_vendor_id' => $carrierRA->getId(),
            ]
        );

        $contractorRA2 = $this->newReserveAccount(
            $contractor,
            [
                'priority' => '1',
                'min_balance' => '500',
                'contribution_amount' => '125',
                'initial_balance' => '500',
                'current_balance' => '500',
                'balance' => '500',
                'starting_balance' => '500',
                'verify_balance' => '500',
                'reserve_account_vendor_id' => $carrierRA->getId(),
            ]
        );

        $paymentSetup = $this->newPaymentSetup(
            $carrier,
            [
                'rate' => '1300.12',
            ]
        );
        $deductionSetup = $this->newDeductionSetup(
            $carrier,
            [
                'rate' => '200.12',
            ]
        );
        $cycle = $this->newCycle(
            $carrier,
            [
                'cycle_period_id' => Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID,
                'cycle_start_date' => '2014-06-01',
                'cycle_close_date' => '2014-06-08',
            ]
        );

        Application_Model_Entity_Accounts_User::login($this->_myUser);
        $cycle->verify();
        $payment = $this->newPayment($contractor, $paymentSetup, $cycle);
        $deduction = $this->newDeduction($contractor, $deductionSetup, $cycle);

        $cycle->process();
        $cycle->approve();
        $analytic = $this->getCycleActions($cycle);

        $this->assertEquals(
            $analytic['cycle']['status_id'],
            Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID
        );

        $this->assertEquals(
            [
                '1',
                '1',
                '1',
                '4',
            ],
            [
                is_countable($analytic['payments']) ? count($analytic['payments']) : 0,
                is_countable($analytic['deductions']) ? count($analytic['deductions']) : 0,
                is_countable($analytic['transactions']) ? count($analytic['transactions']) : 0,
                is_countable($analytic['disbursements']) ? count($analytic['disbursements']) : 0,
            ]
        );

        $this->assertEquals($analytic['payments'][0]['amount'], '1300.12');
        $this->assertEquals($analytic['payments'][0]['contractor_id'], $contractor->getData('entity_id'));
        $this->assertEquals($analytic['payments'][0]['carrier_id'], $carrier->getData('entity_id'));

        $this->assertEquals($analytic['deductions'][0]['amount'], '200.12');
        $this->assertEquals($analytic['deductions'][0]['contractor_id'], $contractor->getData('entity_id'));
        $this->assertEquals($analytic['deductions'][0]['provider_id'], $carrier->getData('entity_id'));

        $this->assertEquals($analytic['transactions'][0]['amount'], '100.00');
        $this->assertEquals(
            $analytic['transactions'][0]['type'],
            Application_Model_Entity_System_ReserveTransactionTypes::CONTRIBUTION
        );
        $this->assertEquals(
            $analytic['transactions'][0]['reserve_account_contractor'],
            $contractorRA->getData('reserve_account_id')
        );

        $this->assertEquals(
            $analytic['disbursements'][0]['process_type'],
            Application_Model_Entity_System_DisbursementTransactionTypes::PAYMENT_TRANSACTION_TYPE
        );
        $account = (new Application_Model_Entity_Accounts_Bank_History())->load(
            $analytic['disbursements'][0]['bank_account_history_id']
        );
        $this->assertEquals($account->getData('bank_account_id'), $contractorBA1->getId());
        $this->assertEquals($analytic['disbursements'][0]['amount'], '500');
        $this->assertEquals($analytic['disbursements'][0]['entity_id'], $contractor->getData('entity_id'));

        $this->assertEquals(
            $analytic['disbursements'][1]['process_type'],
            Application_Model_Entity_System_DisbursementTransactionTypes::PAYMENT_TRANSACTION_TYPE
        );
        $account = (new Application_Model_Entity_Accounts_Bank_History())->load(
            $analytic['disbursements'][1]['bank_account_history_id']
        );
        $this->assertEquals($account->getData('bank_account_id'), $contractorBA2->getId());
        $this->assertEquals($analytic['disbursements'][1]['amount'], '300');
        $this->assertEquals($analytic['disbursements'][1]['entity_id'], $contractor->getData('entity_id'));

        $this->assertEquals(
            $analytic['disbursements'][2]['process_type'],
            Application_Model_Entity_System_DisbursementTransactionTypes::PAYMENT_TRANSACTION_TYPE
        );
        $account = (new Application_Model_Entity_Accounts_Bank_History())->load(
            $analytic['disbursements'][2]['bank_account_history_id']
        );
        $this->assertEquals($account->getData('bank_account_id'), $contractorBA3->getId());
        $this->assertEquals($analytic['disbursements'][2]['amount'], '200');
        $this->assertEquals($analytic['disbursements'][2]['entity_id'], $contractor->getData('entity_id'));

        $this->assertEquals(
            $analytic['disbursements'][3]['process_type'],
            Application_Model_Entity_System_DisbursementTransactionTypes::DEDUCTION_TRANSACTION_TYPE
        );
        $account = (new Application_Model_Entity_Accounts_Bank_History())->load(
            $analytic['disbursements'][3]['bank_account_history_id']
        );
        $this->assertEquals($account->getData('bank_account_id'), $carrierBA->getId());
        $this->assertEquals($analytic['disbursements'][3]['amount'], '300.12');
        $this->assertEquals($analytic['disbursements'][3]['entity_id'], $carrier->getData('entity_id'));
    }

    public function testSettlementRAWithdrawals()
    {
        /* Test Model
         * Compensation $900
         *    Deduction1  $800; Eligible: RA Carrier1; Prior: 1
         *    Deduction2 $1300; Eligible: RA Vendor1;  Prior: 2
         * Contractor         Prior     Cur     Min     Cont
         *      RAonCarrier1:   1      $1000    $700     $50
         *      RAonVendor1 :   2      $1000    $100     $55
         */
        $carrier = $this->newCarrier([], false);
        $carrierBA = $this->newBankAccount($carrier);
        $carrierRA = $this->newReserveAccount($carrier);

        $contractor = $this->newContractor($carrier, [], false);
        $contractorBA = $this->newBankAccount($contractor);

        $vendor = $this->newVendor($carrier, [], false, $contractor);
        $vendorBA = $this->newBankAccount($vendor);
        $vendorRA = $this->newReserveAccount($vendor);

        $contractorRA1 = $this->newReserveAccount(
            $contractor,
            [
                'min_balance' => '700',
                'contribution_amount' => '50',
                'initial_balance' => '1000',
                'current_balance' => '2000',
                'balance' => '2000',
                'starting_balance' => '2000',
                'verify_balance' => '2000',
                'reserve_account_vendor_id' => $carrierRA->getId(),
            ]
        );

        $contractorRA2 = $this->newReserveAccount(
            $contractor,
            [
                'min_balance' => '100',
                'contribution_amount' => '60',
                'initial_balance' => '2000',
                'current_balance' => '2000',
                'balance' => '2000',
                'starting_balance' => '2000',
                'verify_balance' => '2000',
                'reserve_account_vendor_id' => $vendorRA->getId(),
            ]
        );

        $paymentSetup = $this->newPaymentSetup(
            $carrier,
            [
                'rate' => '500',
            ]
        );
        $deductionSetup1 = $this->newDeductionSetup(
            $carrier,
            [
                'reserve_account_receiver' => $carrierRA->getData('reserve_account_id'),
                'eligible' => '1',
                'rate' => '800',
            ]
        );
        $deductionSetup2 = $this->newDeductionSetup(
            $vendor,
            [
                'reserve_account_receiver' => $vendorRA->getData('reserve_account_id'),
                'eligible' => '1',
                'rate' => '1300',
            ]
        );
        $cycle = $this->newCycle(
            $carrier,
            [
                'cycle_period_id' => Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID,
                'cycle_start_date' => '2014-06-01',
                'cycle_close_date' => '2014-06-08',
            ]
        );

        Application_Model_Entity_Accounts_User::login($this->_myUser);
        $cycle->verify();
        $payment = $this->newPayment($contractor, $paymentSetup, $cycle);
        $deduction1 = $this->newDeduction($contractor, $deductionSetup1, $cycle);
        $deduction2 = $this->newDeduction($contractor, $deductionSetup2, $cycle);

        $this->assertEquals($carrierRA->getData('current_balance'), $contractorRA1->getData('current_balance'));
        $this->assertEquals($vendorRA->getData('current_balance'), $contractorRA2->getData('current_balance'));
        $MonolithBefore = $this->getMonolith();

        $cycle->process();
        $cycle->approve();

        $MonolithBefore['transactions'] = $MonolithBefore['transactions'] + 2;
        $MonolithBefore['disbursements'] = $MonolithBefore['disbursements'] + 2;
        $MonolithAfter = $this->getMonolith();
        $this->assertEquals($MonolithBefore, $MonolithAfter);

        $analytics = $this->getCycleActions($cycle);

        $this->assertEquals(
            $analytics['cycle']['status_id'],
            Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID
        );

        $this->assertEquals(
            [
                '1',
                '2',
                '2',
                '2',
            ],
            [
                is_countable($analytics['payments']) ? count($analytics['payments']) : 0,
                is_countable($analytics['deductions']) ? count($analytics['deductions']) : 0,
                is_countable($analytics['transactions']) ? count($analytics['transactions']) : 0,
                is_countable($analytics['disbursements']) ? count($analytics['disbursements']) : 0,
            ]
        );

        $this->assertEquals($analytics['payments'][0]['amount'], '500');
        $this->assertEquals($analytics['payments'][0]['contractor_id'], $contractor->getData('entity_id'));
        $this->assertEquals($analytics['payments'][0]['carrier_id'], $carrier->getData('entity_id'));

        $this->assertEquals($analytics['deductions'][0]['amount'], '800');
        $this->assertEquals($analytics['deductions'][0]['contractor_id'], $contractor->getData('entity_id'));
        $this->assertEquals($analytics['deductions'][0]['eligible'], '1');
        $this->assertEquals(
            $analytics['deductions'][0]['reserve_account_receiver'],
            $carrierRA->getData('reserve_account_id')
        );

        $this->assertEquals($analytics['deductions'][1]['amount'], '1300');
        $this->assertEquals($analytics['deductions'][1]['contractor_id'], $contractor->getData('entity_id'));
        $this->assertEquals($analytics['deductions'][1]['eligible'], '1');
        $this->assertEquals(
            $analytics['deductions'][1]['reserve_account_receiver'],
            $vendorRA->getData('reserve_account_id')
        );

        $this->assertEquals($analytics['transactions'][0]['amount'], '300');
        $this->assertEquals(
            $analytics['transactions'][0]['type'],
            Application_Model_Entity_System_ReserveTransactionTypes::WITHDRAWAL
        );
        $this->assertEquals(
            $analytics['transactions'][0]['reserve_account_contractor'],
            $contractorRA1->getData('reserve_account_id')
        );
        $this->assertEquals(
            $analytics['transactions'][0]['reserve_account_vendor'],
            $carrierRA->getData('reserve_account_id')
        );
        $this->assertEquals(
            $analytics['transactions'][0]['deduction_id'],
            $deduction1->getId()
        );

        $this->assertEquals($analytics['transactions'][1]['amount'], '1300');
        $this->assertEquals(
            $analytics['transactions'][1]['type'],
            Application_Model_Entity_System_ReserveTransactionTypes::WITHDRAWAL
        );
        $this->assertEquals(
            $analytics['transactions'][1]['reserve_account_contractor'],
            $contractorRA2->getData('reserve_account_id')
        );
        $this->assertEquals(
            $analytics['transactions'][1]['reserve_account_vendor'],
            $vendorRA->getData('reserve_account_id')
        );
        $this->assertEquals(
            $analytics['transactions'][1]['deduction_id'],
            $deduction2->getId()
        );

        $this->assertEquals(
            $analytics['disbursements'][0]['process_type'],
            Application_Model_Entity_System_DisbursementTransactionTypes::DEDUCTION_TRANSACTION_TYPE
        );
        $account = (new Application_Model_Entity_Accounts_Bank_History())->load(
            $analytics['disbursements'][0]['bank_account_history_id']
        );
        $this->assertEquals($account->getData('bank_account_id'), $carrierBA->getId());
        $this->assertEquals($analytics['disbursements'][0]['amount'], '1800');
        $this->assertEquals($analytics['disbursements'][0]['entity_id'], $carrier->getData('entity_id'));

        $this->assertEquals(
            $analytics['disbursements'][1]['process_type'],
            Application_Model_Entity_System_DisbursementTransactionTypes::DEDUCTION_TRANSACTION_TYPE
        );
        $account = (new Application_Model_Entity_Accounts_Bank_History())->load(
            $analytics['disbursements'][1]['bank_account_history_id']
        );
        $this->assertEquals($account->getData('bank_account_id'), $vendorBA->getId());
        $this->assertEquals($analytics['disbursements'][1]['amount'], '-1300');
        $this->assertEquals($analytics['disbursements'][1]['entity_id'], $vendor->getData('entity_id'));
    }

    public function testReserveAccountBalance()
    {
        $carrier = $this->newCarrier([], false);
        $carrierBA = $this->newBankAccount($carrier);
        $carrierRA = $this->newReserveAccount(
            $carrier,
            [
                'priority' => '0',
                'min_balance' => '100000',
                'contribution_amount' => '200',
                'initial_balance' => '0',
                'current_balance' => '0',
                'balance' => '0',
                'starting_balance' => '0',
                'verify_balance' => '0',
            ]
        );

        $contractor = $this->newContractor($carrier, [], false);
        $contractorBA = $this->newBankAccount($contractor);
        $contractorRA = $this->newReserveAccount(
            $contractor,
            [
                'priority' => '0',
                'min_balance' => '10000',
                'contribution_amount' => '100',
                'initial_balance' => '0',
                'current_balance' => '0',
                'balance' => '0',
                'starting_balance' => '0',
                'verify_balance' => '0',
                'reserve_account_vendor_id' => $carrierRA->getId(),
            ]
        );

        $paymentSetup = $this->newPaymentSetup(
            $carrier,
            [
                'quantity' => '1',
                'rate' => '1000',
            ]
        );

        $deductionSetup = $this->newDeductionSetup(
            $carrier,
            [
                'quantity' => '1',
                'rate' => '200',
            ]
        );

        $cycle = $this->newCycle($carrier);

        Application_Model_Entity_Accounts_User::login($this->_myUser);
        $cycle->verify();
        $payment = $this->newPayment($contractor, $paymentSetup, $cycle);
        $deduction = $this->newDeduction($contractor, $deductionSetup, $cycle);

        $cycle->process();
        $cycle->approve();
        $analytic = $this->getCycleActions($cycle);

        $this->assertEquals(
            [
                '1',
                '1',
                '1',
            ],
            [
                is_countable($analytic['payments']) ? count($analytic['payments']) : 0,
                is_countable($analytic['deductions']) ? count($analytic['deductions']) : 0,
                is_countable($analytic['transactions']) ? count($analytic['transactions']) : 0,
            ]
        );

        $this->assertEquals($analytic['transactions'][0]['amount'], '100.00');
        $this->assertEquals(
            $analytic['transactions'][0]['type'],
            Application_Model_Entity_System_ReserveTransactionTypes::CONTRIBUTION
        );
        $this->assertEquals(
            $analytic['transactions'][0]['reserve_account_contractor'],
            $contractorRA->getData('reserve_account_id')
        );

        $carrierRA_id = $carrierRA->getData('reserve_account_id');
        $carrierRA = (new Application_Model_Entity_Accounts_Reserve())->load($carrierRA_id);
        $this->assertEquals($carrierRA->getData('current_balance'), '100.00');
        $this->assertEquals($carrierRA->getData('balance'), '0');

        $contractorRA = (new Application_Model_Entity_Accounts_Reserve_Contractor())->load($contractorRA->getId());
        $this->assertEquals($contractorRA->getData('current_balance'), '100.00');
        $this->assertEquals($contractorRA->getData('balance'), '0');

        /**
         * @var $newCycle Application_Model_Entity_Settlement_Cycle
         */
        $newCycle = (new Application_Model_Entity_Settlement_Cycle())->getCollection()
            ->addFilter('parent_cycle_id', $cycle->getId())
            ->getFirstItem();

        $newCycle->verify();
        $this->newPayment($contractor, $paymentSetup, $newCycle, ['rate' => '29']);
        $newCycle->process();
        $newCycle->approve();

        $analytic = $this->getCycleActions($newCycle);

        $this->assertEquals(
            [
                '1',
                '0',
                '1',
            ],
            [
                is_countable($analytic['payments']) ? count($analytic['payments']) : 0,
                is_countable($analytic['deductions']) ? count($analytic['deductions']) : 0,
                is_countable($analytic['transactions']) ? count($analytic['transactions']) : 0,
            ]
        );

        $carrierRA = (new Application_Model_Entity_Accounts_Reserve())->load($carrierRA_id);
        $this->assertEquals($carrierRA->getData('current_balance'), '129');

        $contractorRA = (new Application_Model_Entity_Accounts_Reserve_Contractor())->load($contractorRA->getId());
        $this->assertEquals($contractorRA->getData('current_balance'), '129');
    }
}
