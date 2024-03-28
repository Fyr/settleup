<?php

class Reserve_TransactionsControllerTest extends BaseTestCase
{
    /** @var Reserve_TransactionsController */
    private $controller;

    protected function setUp(): void
    {
        $this->setDefaultController('reserve_transactions');
        parent::setUp();
    }

    public static $carrier;
    public static $carrierRA;
    public static $contractor;
    public static $contractorRA;
    public static $RT;
    public static $cycle;

    public function testListAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'list'],
            ]
        );
    }

    public function testIndexAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'index'],
                'assert' => ['action' => 'list'],
            ]
        );
    }

    public function testNewActionNewTransactionContribution()
    {
        self::$carrier = $this->newCarrier();
        self::$carrierRA = $this->newReserveAccount(self::$carrier);

        self::$contractor = $this->newContractor(self::$carrier);
        self::$contractorRA = $this->newReserveAccount(
            self::$contractor,
            [
                'priority' => '1',
                'min_balance' => '500',
                'contribution_amount' => '50',
                'initial_balance' => '300',
                'current_balance' => '300',
                'balance' => '300',
                'starting_balance' => '300',
                'verify_balance' => '300',
                'reserve_account_vendor_id' => self::$carrierRA->getId(),
            ]
        );

        self::$cycle = $this->newCycle(self::$carrier);
        Application_Model_Entity_Accounts_User::login($this->_myUser);
        self::$cycle->verify();

        $lastRA_Id = (new Application_Model_Entity_Accounts_Reserve_Transaction())->getCollection()
            ->getLastItem()
            ->getId();
        $this->baseTestAction(
            [
                'params' => ['action' => 'new'],
                'ajax' => [
                    'selectedSetup' => [self::$contractorRA->getData('reserve_account_id')],
                    'selectedContractors' => [self::$contractor->getData('entity_id')],
                    'selectedCycle' => self::$cycle->getId(),
                    'type' => Application_Model_Entity_System_ReserveTransactionTypes::CONTRIBUTION,
                ],
            ]
        );

        self::$RT = (new Application_Model_Entity_Accounts_Reserve_Transaction())->getCollection()
            ->getLastItem();
        $this->assertEquals($lastRA_Id + 1, self::$RT->getId());

        $this->assertEquals(
            self::$RT->getData('reserve_account_contractor'),
            self::$contractorRA->getData('reserve_account_id')
        );

        $this->assertEquals(
            self::$RT->getData('reserve_account_vendor'),
            self::$carrierRA->getData('reserve_account_id')
        );

        $this->assertEquals(
            self::$RT->getData('type'),
            Application_Model_Entity_System_ReserveTransactionTypes::CONTRIBUTION
        );

        $this->assertEquals(
            self::$RT->getData('contractor_id'),
            self::$contractor->getData('entity_id')
        );

        $this->assertEquals(
            self::$RT->getData('vendor_entity_id'),
            self::$carrier->getData('entity_id')
        );
        $this->assertEquals(
            self::$RT->getData('settlement_cycle_id'),
            self::$cycle->getId()
        );

        $this->assertEquals(
            self::$RT->getData('amount'),
            self::$contractorRA->getReserveAccountEntity()
                ->getData('contribution_amount')
        );

        $contractorRAEntity = self::$contractorRA->getReserveAccountEntity();
        $this->assertEquals(
            $contractorRAEntity->getData('current_balance'),
            $contractorRAEntity->getData('contribution_amount') + $contractorRAEntity->getData('initial_balance')
        );
    }

    public function testEditActionNewTransaction()
    {
        if (!self::$RT) {
            self::$RT = (new Application_Model_Entity_Accounts_Reserve_Transaction())->getCollection()
                ->getLastItem();
        }
        $post = [
            'id' => self::$RT->getId(),
            'reserve_account_contractor' => self::$contractorRA->getData('reserve_account_id'),
            'reserve_account_vendor' => self::$carrierRA->getData('reserve_account_id'),
            'reserve_account_vendor_title' => 'CarReserveAc',
            'type' => Application_Model_Entity_System_ReserveTransactionTypes::CONTRIBUTION,
            'approved_by' => '',
            'contractor_id' => self::$contractor->getData('entity_id'),
            'company_name' => 'PHPUnitRT Company',
            'vendor_name' => 'PHPUnitRT VendorName',
            'type_title' => 'Withdrawal',
            'reserve_code' => 'CarReserveCode',
            'description' => 'PHPUnit reserve transaction POST' . random_int(1, 32000),
            'disbursement_id' => '',
            'deduction_id' => '',
            'amount' => '100.00',
            'approved_datetime' => '',
            'approved_by_title' => '',
            'created_datetime' => '',
            'created_by_title' => '',
            'settlement_status' => 'Processed',
            'settlement_cycle_id' => self::$cycle->getId(),
            'back' => 'index',
            'submit' => 'Save',
        ];

        $lastRA_Id_before = (new Application_Model_Entity_Accounts_Reserve_Transaction())->getCollection()
            ->getLastItem()
            ->getId();

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $post,
            ]
        );

        $lastRA_Id_after = (new Application_Model_Entity_Accounts_Reserve_Transaction())->getCollection()
            ->getLastItem()
            ->getId();

        $this->assertEquals($lastRA_Id_before, $lastRA_Id_after);

        self::$RT = (new Application_Model_Entity_Accounts_Reserve_Transaction())->load($lastRA_Id_after);

        $this->assertEquals(self::$RT->getData('amount'), '100.00');

        return $post;
    }

    /**
     * @depends testEditActionNewTransaction
     */
    public function testEditActionEditTransaction(array $data)
    {
        $transaction = new Application_Model_Entity_Accounts_Reserve_Transaction();
        $transaction->load($data['description'], 'description');

        $data['id'] = $transaction->getId();
        $data['description'] = $data['description'] . ' EDIT';
        $data['contractor'] = (new Application_Model_Entity_Entity_Contractor())->getCollection()
            ->getFirstItem()
            ->getId();
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $reserveTransactions = new Application_Model_Entity_Accounts_Reserve_Transaction();
        $reserveTransaction = $reserveTransactions->load($data['description'], 'description');
        $this->assertNotNull($reserveTransaction->getId());
        return $data;
    }

    /**
     * @depends testEditActionNewTransaction
     */
    public function testEditActionGetNoId(array $data)
    {
        $data['id'] = '-1';
        $data['account'] = (new Application_Model_Entity_Accounts_Reserve_Contractor())->getCollection()
            ->getFirstItem()
            ->getReserveAccountId();
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => $data,
            ]
        );
    }

    /**
     * @depends testEditActionEditTransaction
     */
    public function testEditActionNotValid(array $data)
    {
        $reserveTransactions = new Application_Model_Entity_Accounts_Reserve_Transaction();
        $reserveTransactionIdBefore = $reserveTransactions->getCollection()
            ->getLastItem()
            ->getId();
        $data['reserve_account_vendor'] = '';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $reserveTransactionIdAfter = $reserveTransactions->getCollection()
            ->getLastItem()
            ->getId();
        $this->assertEquals($reserveTransactionIdBefore, $reserveTransactionIdAfter);
    }

    /**
     * @depends testEditActionEditTransaction
     */
    //    public function testApproveAction(array $data)
    //    {
    //        $this->baseTestAction(
    //            array(
    //                'params' => array('action' => 'approve'),
    //                'get'    => array('id'     => $data['id']),
    //            )
    //        );
    //        $reserveTransactions = new Application_Model_Entity_Accounts_Reserve_Transaction();
    //        $reserveTransactionStatus = $reserveTransactions->load($data['id'])->getStatus();
    //        $this->assertEquals($reserveTransactionStatus, Application_Model_Entity_Payments_Payment::STATUS_APPROVED_ID);
    //    }

    /**
     * @depends testEditActionEditTransaction
     */
    public function testEditAfterApproveAction(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => ['id' => $data['id']],
            ]
        );
    }

    /**
     * @depends testEditActionEditTransaction
     */
    public function testMultiDeleteAction(array $data)
    {
        unset($data['id']);
        $newRT = (new Application_Model_Entity_Accounts_Reserve_Transaction())->setData($data)
            ->save();
        (new Application_Model_Entity_Accounts_Reserve_Transaction())->load(self::$RT->getId())
            ->setDeleted('0')
            ->save();
        (new Application_Model_Entity_Accounts_Reserve_Transaction())->load($newRT->getId())
            ->setDeleted('0')
            ->save();

        $this->baseTestAction(
            [
                'params' => ['action' => 'multiaction'],
                'ajax' => [
                    'action-type' => 'delete',
                    'ids' => self::$RT->getId() . ',' . $newRT->getId(),
                ],
            ]
        );

        $this->assertEquals(
            (new Application_Model_Entity_Accounts_Reserve_Transaction())->load(self::$RT->getId())
                ->getData('deleted'),
            '0'
        );
        $this->assertEquals(
            (new Application_Model_Entity_Accounts_Reserve_Transaction())->load($newRT->getId())
                ->getData('deleted'),
            '0'
        );
    }

    public function testUpdaterasetupActionGet()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'updaterasetup'],
                'get' => [],
            ]
        );
    }

    public function testUpdaterasetupAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'updaterasetup'],
                'ajax' => ['raId' => '1'],
            ]
        );
    }

    public function testListActionProcessingStatusCycle()
    {
        $cycle = (new Application_Model_Entity_Settlement_Cycle())->getCollection()
            ->getLastItem()
            ->setData('status_id', Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID)
            ->save();
        $this->getRequest()
            ->setCookie('settlement_cycle_id', $cycle->getId());
        $this->baseTestAction(
            [
                'params' => ['action' => 'list'],
                'get' => ['isAjax' => 'true'],
            ],
            true
        );
    }
}
