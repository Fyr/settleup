<?php

class Settlement_IndexControllerTest extends BaseTestCase
{
    /** @var Settlement_IndexController */
    private $controller;
    public static $user;
    public static $carrier;
    public static $contractor;
    public static $contribution;
    public static $cycle;

    protected function setUp(): void
    {
        $this->setDefaultController('settlement_index');
        parent::setUp();
    }

    public function testIndexAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'index'],
            ]
        );
    }

    public function testNewAction()
    {
        Application_Model_Entity_Accounts_User::login($this->_myUser);
        $this->setStorage();
        $rule = (new Application_Model_Entity_Entity_Carrier())->getCycleRule();
        $data = [
            'params' => ['action' => 'new'],
        ];

        if ($rule->getId()) {
            $data['assert'] = ['action' => 'edit'];
        }
        $this->baseTestAction(
            $data,
            false
        );
    }

    public function testEditActionNewCycleNotValid()
    {
        $lastCycleIdBefore = (new Application_Model_Entity_Settlement_Cycle())->getCollection()
            ->getLastItem()
            ->getId();
        $post = [
            'id' => '',
            'carrier_id' => '',
            'cycle_start_date' => '',
            'cycle_period_id' => '',
            'first_start_day' => '',
            'second_start_day' => '',
            'payment_terms' => '',
            'disbursement_terms' => '',
            'submit' => 'Save',
        ];

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $post,
            ]
        );
        $lastCycleIdAfter = (new Application_Model_Entity_Settlement_Cycle())->getCollection()
            ->getLastItem()
            ->getId();
        $this->assertEquals($lastCycleIdBefore, $lastCycleIdAfter);
    }

    public function testEditActionNewRule()
    {
        self::$carrier = $this->newCarrier();
        $post = [
            'id' => '',
            'cycle_period_id' => '1',
            'change_cycle_rule_fields' => '{"cycle_start_date":"06/20/2014","cycle_period_id":"3","billing_cycle_id":"3"}',
            'cycle_start_date' => '01-01-2014',
            'first_start_day' => '1',
            'second_start_day' => '15',
            'payment_terms' => random_int(1, 1000),
            'disbursement_terms' => '1',
            'submit' => 'Save',
        ];
        $this->baseTestAction(
            [
                'params' => ['controller' => 'settlement_rule', 'action' => 'edit'],
                'post' => $post,
            ]
        );
        return $post;
    }

    public function testEditActionNewCycle()
    {
        $lastCycleIdBefore = (new Application_Model_Entity_Settlement_Cycle())->getCollection()
            ->getLastItem()
            ->getId();

        $post = [
            'id' => '',
            'carrier_id' => self::$carrier->getData('entity_id'),
            'cycle_start_date' => '06-02-2014',
            'cycle_close_date' => '06-08-2014',
            'processing_date' => '06-03-2014',
            'disbursement_date' => '06-09-2014',
            'cycle_period_id' => Application_Model_Entity_System_CyclePeriod::SEMY_MONTHLY_PERIOD_ID,
            'first_start_day' => '1',
            'second_start_day' => '15',
            'submit' => 'varSave',
        ];

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $post,
            ]
        );
        $lastCycleIdAfter = (new Application_Model_Entity_Settlement_Cycle())->getCollection()
            ->getLastItem()
            ->getId();
        $this->assertNotEquals($lastCycleIdBefore, $lastCycleIdAfter);
        $post['id'] = $lastCycleIdAfter;
        return $post;
    }

    /**
     * @depends testEditActionNewCycle
     */
    public function testEditAction(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => ['id' => $data['id']],
            ]
        );
        return $data;
    }

    /**
     * @depends testEditActionNewCycle
     */
    public function testVerifyAction(array $data)
    {
        $cycleId = $data['id'];
        $this->getRequest()
            ->setCookie('settlement_cycle_id', $data['id']);
        $this->baseTestAction(
            [
                'params' => ['action' => 'verify'],
                'get' => ['id' => $data['id']],
            ]
        );
        $status = (new Application_Model_Entity_Settlement_Cycle())->load($data['id'])
            ->getStatusId();
        $this->assertEquals($status, Application_Model_Entity_System_SettlementCycleStatus::VERIFIED_STATUS_ID);
        return $cycleId;
    }

    /**
     * @depends testVerifyAction
     */
    public function testIndexActionVerifyCycle($cycleId)
    {
        Application_Model_Entity_Accounts_User::login(16);
        $this->getRequest()
            ->setCookie('settlement_cycle_id', $cycleId);
        $this->baseTestAction(
            [
                'params' => ['action' => 'index'],
            ]
        );
    }

    /**
     * @depends testEditActionNewCycle
     */
    public function testProcessAction(array $data)
    {
        $this->getRequest()
            ->setCookie('settlement_cycle_id', $data['id']);
        Application_Model_Entity_Accounts_User::login($this->_myUser);
        self::$contractor = $this->newContractor(self::$carrier);
        $deductionSetup = $this->newDeductionSetup(
            self::$carrier,
            [
                'rate' => '100',
                'terms' => '1',
                'amount' => '100',
            ]
        );
        $deduction = $this->newDeduction(
            self::$contractor,
            $deductionSetup,
            (new Application_Model_Entity_Settlement_Cycle())->load($data['id']),
            ['adjusted_balance' => '0']
        );

        $carrierRA = $this->newReserveAccount(self::$carrier);
        $contractorRA = $this->newReserveAccount(
            self::$contractor,
            [
                'priority' => '1',
                'min_balance' => '1000',
                'contribution_amount' => '500',
                'initial_balance' => '1000',
                'current_balance' => '1000',
                'balance' => '1000',
                'starting_balance' => '1000',
                'verify_balance' => '1000',
                'reserve_account_vendor_id' => $carrierRA->getId(),
            ]
        );
        self::$contribution = $this->newReserveTransaction(
            $data['id'],
            $contractorRA,
            $carrierRA,
            Application_Model_Entity_System_ReserveTransactionTypes::CONTRIBUTION,
            $deduction,
            [
                'amount' => '200',
                'balance' => '200',
            ]
        );

        $this->baseTestAction(
            [
                'params' => ['action' => 'process'],
                'get' => ['id' => $data['id']],
            ]
        );

        $status = (new Application_Model_Entity_Settlement_Cycle())->load($data['id'])
            ->getStatusId();

        $this->assertEquals($status, Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID);
    }

    /**
     * @depends testEditActionNewCycle
     */
    public function testIndexActionProcessedStatusNegativeMessages($data)
    {
        $reserveAccountHistory = (new Application_Model_Entity_Accounts_Reserve_History())->getCollection()
            ->addFilter('settlement_cycle_id', $data['id'])
            ->getFirstItem();
        $reserveAccountHistory->setCurrentBalance('-1')
            ->save();

        $this->baseTestAction(
            [
                'params' => ['action' => 'index'],
            ]
        );
        self::$contribution->setDeleted('1')
            ->save();
        $reserveAccountHistory->delete();
    }

    /**
     * @depends testEditActionNewCycle
     */
    public function testRejectAction(array $data)
    {
        $this->getRequest()
            ->setCookie('settlement_cycle_id', $data['id']);
        $this->baseTestAction(
            [
                'params' => ['action' => 'reject'],
                'get' => ['id' => $data['id']],
            ]
        );
        $status = (new Application_Model_Entity_Settlement_Cycle())->load($data['id'])
            ->getStatusId();
        $this->assertEquals($status, Application_Model_Entity_System_SettlementCycleStatus::VERIFIED_STATUS_ID);
    }

    /**
     * @depends testEditActionNewCycle
     */
    public function testApproveActionCatchError(array $data)
    {
        (new Application_Model_Entity_Settlement_Cycle())->load($data['id'])
            ->setStatusId(Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID)
            ->save();
        $this->baseTestAction(
            [
                'params' => ['action' => 'approve'],
                'get' => ['id' => $data['id']],
                'assert' => [
                    'controller' => 'settlement_index',
                    'action' => 'approve',
                ],
            ]
        );
        $status = (new Application_Model_Entity_Settlement_Cycle())->load($data['id'])
            ->getStatusId();
        $this->assertEquals($status, Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID);
    }

    /**
     * @depends testEditActionNewCycle
     */
    public function testApproveAction(array $data)
    {
        $this->getRequest()
            ->setCookie('settlement_cycle_id', $data['id']);
        (new Application_Model_Entity_Settlement_Cycle())->load($data['id'])
            ->setStatusId(Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID)
            ->save();
        $this->baseTestAction(
            [
                'params' => ['action' => 'approve'],
                'get' => ['id' => $data['id']],
            ]
        );
        $status = (new Application_Model_Entity_Settlement_Cycle())->load($data['id'])
            ->getStatusId();
        $this->assertEquals($status, Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID);
    }

    public function testContractorAction()
    {
        $this->loginUser();
        $cycle = (new Application_Model_Entity_Settlement_Cycle())->getCollection()
            ->getLastItem()
            ->setData('status_id', Application_Model_Entity_System_SettlementCycleStatus::VERIFIED_STATUS_ID)
            ->save();
        $this->getRequest()
            ->setCookie('settlement_cycle_id', $cycle->getId());
        $contractor = (new Application_Model_Entity_Entity_Contractor())->getCollection()
            ->getLastItem();
        $contractorId = $contractor->getId();
        $contractorEntityId = $contractor->getEntityId();
        $this->baseTestAction(
            [
                'params' => ['action' => 'contractor'],
                'get' => ['id' => $contractorEntityId],
            ],
            false
        );
    }

    public function testDeleteCycle()
    {
        $carrier = $this->newCarrier();
        $contractor = $this->newContractor($carrier);

        $carrierRA = $this->newReserveAccount($carrier);
        $contractorRA = $this->newReserveAccount(
            $contractor,
            [
                'reserve_account_vendor_id' => $carrierRA->getId(),
            ]
        );

        $paymentSetup = $this->newPaymentSetup(
            $carrier,
            [
                'rate' => '1100',
                'terms' => '1',
                'amount' => '1100',
            ]
        );

        $deductionSetup = $this->newDeductionSetup(
            $carrier,
            [
                'rate' => '500',
                'terms' => '1',
                'amount' => '500',
            ]
        );

        Application_Model_Entity_Accounts_User::login($this->_myUser);
        $this->setStorage();

        $cycle = $this->newCycle($carrier);
        $cycle->verify();

        $payment = $this->newPayment($contractor, $paymentSetup, $cycle);
        $deduction = $this->newDeduction($contractor, $deductionSetup, $cycle);
        $transaction = $this->newReserveTransaction(
            $cycle,
            $contractorRA,
            $carrierRA,
            Application_Model_Entity_System_ReserveTransactionTypes::CONTRIBUTION,
            $deduction,
            [
                'amount' => '100',
                'balance' => '100',
            ]
        );

        $monolith = $this->getMonolith();
        $analytics = $this->getCycleActions($cycle);

        $this->assertEquals(
            [
                '1',
                '1',
                '1',
                '0',
            ],
            [
                is_countable($analytics['payments']) ? count($analytics['payments']) : 0,
                is_countable($analytics['deductions']) ? count($analytics['deductions']) : 0,
                is_countable($analytics['transactions']) ? count($analytics['transactions']) : 0,
                is_countable($analytics['disbursements']) ? count($analytics['disbursements']) : 0,
            ]
        );

        $this->getRequest()
            ->setCookie('settlement_cycle_id', $cycle->getId());
        $this->baseTestAction(
            [
                'params' => ['action' => 'delete'],
                'get' => ['id' => $cycle->getId()],
            ]
        );

        $NewMonolith = $this->getMonolith();
        $this->assertEquals($monolith, $NewMonolith);

        $analytics = $this->getCycleActions($cycle);
        $deleted_cycle = (new Application_Model_Entity_Settlement_Cycle())->getCollection()
            ->addFilter('parent_cycle_id', $cycle->getId())
            ->getFirstItem();
        $this->assertEquals($deleted_cycle->getData('deleted'), '1');
        $this->assertEquals($analytics['payments'][0]['deleted'], '1');
        $this->assertEquals($analytics['deductions'][0]['deleted'], '1');
        $this->assertEquals($analytics['transactions'][0]['deleted'], '1');

        return $cycle;
    }

    /**
     * @depends  testDeleteCycle
     */
    public function testVerifyAfterDeleteCycle($cycle)
    {
        $this->loginUser();
        $monolith = $this->getMonolith();
        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load($cycle->getId());
        $cycle->verify();

        $newMonolith = $this->getMonolith();
        $monolith['cycles'] = $monolith['cycles'] + 1;
        $this->assertEquals($monolith, $newMonolith);

        $analytics = $this->getCycleActions($cycle);
        $this->assertEquals(
            ['1', '1', '1', '0'],
            [
                is_countable($analytics['payments']) ? count($analytics['payments']) : 0,
                is_countable($analytics['deductions']) ? count($analytics['deductions']) : 0,
                is_countable($analytics['transactions']) ? count($analytics['transactions']) : 0,
                is_countable($analytics['disbursements']) ? count($analytics['disbursements']) : 0,
            ]
        );
        $this->assertEquals(
            ['1', '1', '1'],
            [
                $analytics['payments'][0]['deleted'],
                $analytics['deductions'][0]['deleted'],
                $analytics['transactions'][0]['deleted'],
            ]
        );
    }

    public function testBeforePermissionsTest()
    {
        self::$carrier = $this->newCarrier();
        self::$user = $this->newUser(
            [
                'entity_id' => self::$carrier->getData('entity_id'),
                'role_id' => Application_Model_Entity_System_UserRoles::CARRIER_ROLE_ID,
            ]
        );
        $this->userPermissions(self::$user);
    }

    /**
     * @dataProvider permissionsProvider
     */
    public function testHasNoPermissions($action, $method, $method_params, $permissions, $assert, $function)
    {
        $this->userPermissions(self::$user, $permissions);
        $this->loginUser(self::$user->getId(), $this->defaultPassMd5);
        if ($function) {
            $function();
        }

        $this->baseTestAction(
            [
                'params' => ['action' => $action],
                $method => $method_params,
                'assert' => $assert,
            ],
            false
        );
    }

    public function permissionsProvider()
    {
        return [
            //NO CYCLE
            [
                'action' => 'new',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'settlement_edit' => '0',
                ],
                'assert' => ['action' => 'new'],
                'function' => false,
            ],
            [
                'action' => 'new',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'settlement_edit' => '1',
                    'settlement_rule_manage' => '0',
                ],
                'assert' => ['action' => 'new'],
                'function' => false,
            ],
            [
                'action' => 'edit',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'settlement_edit' => '1',
                ],
                'assert' => ['action' => 'edit'],
                'function' => false,
            ],
            [
                'action' => 'edit',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'settlement_edit' => '0',
                ],
                'assert' => ['action' => 'edit'],
                'function' => false,
            ],
            // CYCLE EXISTS
            [
                'action' => 'new',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'settlement_edit' => '1',
                    'settlement_rule_manage' => '0',
                ],
                'assert' => ['action' => 'edit'],
                'function' => function () {
                    $this->newCycle(self::$carrier);
                },
            ],
            [
                'action' => 'edit',
                'method' => 'get',
                'method_params' => ['id' => '1'],
                'permissions' => [
                    'settlement_edit' => '0',
                ],
                'assert' => ['action' => 'edit'],
                'function' => false,
            ],
            [
                'action' => 'edit',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'settlement_edit' => '1',
                ],
                'assert' => ['action' => 'edit'],
                'function' => false,
            ],
            // CYCLE ACTIONS
            [
                'action' => 'verify',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'settlement_verify' => '0',
                ],
                'assert' => ['action' => 'verify'],
                'function' => false,
            ],
            [
                'action' => 'process',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'settlement_process' => '0',
                ],
                'assert' => ['action' => 'process'],
                'function' => false,
            ],
            [
                'action' => 'reject',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'settlement_reject' => '0',
                ],
                'assert' => ['action' => 'reject'],
                'function' => false,
            ],
            [
                'action' => 'approve',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'settlement_approve' => '0',
                ],
                'assert' => ['action' => 'approve'],
                'function' => false,
            ],
            [
                'action' => 'delete',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'settlement_delete' => '0',
                ],
                'assert' => ['action' => 'delete'],
                'function' => false,
            ],
        ];
    }

    /**
     * @depends testEditActionNewCycle
     */
    public function testApproveActionCycleStageError(array $data)
    {
        $carrier = $this->newCarrier();
        $user = $this->newUser(
            [
                'entity_id' => $carrier->getData('entity_id'),
                'role_id' => Application_Model_Entity_System_UserRoles::CARRIER_ROLE_ID,
            ]
        );
        $this->loginUser($user->getId(), md5('pass'));
        $this->setStorage();
        (new Application_Model_Entity_Settlement_Cycle())->load($data['id'])
            ->setStatusId(Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID)
            ->save();
        $this->baseTestAction(
            [
                'params' => ['action' => 'approve'],
                'get' => ['id' => $data['id']],
            ],
            false
        );
        $status = (new Application_Model_Entity_Settlement_Cycle())->load($data['id'])
            ->getStatusId();
        $this->assertEquals($status, Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID);
    }
}
