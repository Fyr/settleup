<?php

class Transactions_DisbursementControllerTest extends BaseTestCase
{
    /** @var Transactions_DisbursementController */
    private $controller;

    protected function setUp(): void
    {
        $this->setDefaultController('transactions_disbursement');
        parent::setUp();
    }

    public function testIndexAction()
    {
        $carrier = $this->newCarrier();
        $cycle = $this->newCycle($carrier);
        (new Application_Model_Entity_Accounts_User())->load(16)
            ->setData('last_selected_carrier', $carrier->getId())
            ->save();
        $this->baseTestAction(
            [
                'params' => ['action' => 'index'],
                'assert' => ['action' => 'list'],
            ]
        );
    }

    public function testNewAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'new'],
                'assert' => ['action' => 'edit'],
            ]
        );
    }

    public function testListAction()
    {
        $this->getRequest()
            ->setCookie('settlement_cycle_filter_type', Application_Model_Entity_Settlement_Cycle::CURRENT_FILTER_TYPE);
        $this->baseTestAction(
            [
                'params' => ['action' => 'list'],
                'ajax' => [
                    'cycle' => '1',
                    'isAjax' => '1',
                ],
            ]
        );
    }

    public function testEditActionNewDisbursement()
    {
        $carrier = $this->newCarrier();
        (new Application_Model_Entity_Accounts_User())->load(16)
            ->setData('last_selected_carrier', $carrier->getId())
            ->save();
        $cycle = $this->newCycle(
            $carrier,
            [
                'status_id' => Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID,
            ]
        );

        $disbursementIdBefore = (new Application_Model_Entity_Transactions_Disbursement())->getCollection()
            ->getLastItem()
            ->getId();

        $post = [
            'id' => '',
            'entity_id' => '1',
            'entity_id_title' => 'PhpunitEidT',
            'approved_by' => '',
            'bank_account_id_title' => 'Title',
            'settlement_cycle_id' => $cycle->getId(),
            'settlement_cycle_id_title' => '01/07/2014 - 07/07/2014',
            'process_type' => '1',
            'process_type_title' => 'Settlement',
            'created_by' => '16',
            'bank_account_history_id' => '',
            'status' => '',
            'status_title' => 'Not Approved',
            'description' => 'PHPUnit disbursement POST' . random_int(1, 32000),
            'payment_type_title' => 'ACH',
            'ACH_bank_routing_id' => '',
            'disbursement_code' => '1',
            'disbursement_date' => '2014-06-17',
            'amount' => '1000',
            'approved_datetime' => '',
            'approved_by_title' => '',
            'created_datetime' => '',
            'created_by_title' => '',
            'submit' => 'Save',
        ];
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $post,
            ]
        );
        $disbursementIdAfter = (new Application_Model_Entity_Transactions_Disbursement())->getCollection()
            ->getLastItem()
            ->getId();

        $this->assertNotEquals($disbursementIdBefore, $disbursementIdAfter);
        return $post;
    }

    /**
     * @depends testEditActionNewDisbursement
     */
    public function testEditActionEditDisbursement(array $data)
    {
        $disbursement = (new Application_Model_Entity_Transactions_Disbursement())->load(
            $data['description'],
            'description'
        );
        $data['id'] = $disbursement->getId();
        $data['description'] = $data['description'] . 'EDIT';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $this->assertEquals(
            (new Application_Model_Entity_Transactions_Disbursement())->load($data['description'], 'description')
                ->getId(),
            $data['id']
        );
        return $data;
    }

    /**
     * @depends testEditActionEditDisbursement
     */
    public function testEditActionEditGet(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => ['id' => $data['id']],
            ]
        );
    }

    /**
     * @depends testEditActionEditDisbursement
     */
    public function testEditActionEditByCycle(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => ['cycle' => $data['settlement_cycle_id']],
            ]
        );
    }

    /**
     * @depends testEditActionNewDisbursement
     */
    public function testEditActionEditDisbursementNotValid(array $data)
    {
        $idBefore = (new Application_Model_Entity_Transactions_Disbursement())->getCollection()
            ->getLastItem()
            ->getId();
        $data['bank_account_id_title'] = '';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $this->assertEquals(
            $idBefore,
            (new Application_Model_Entity_Transactions_Disbursement())->getCollection()
                ->getLastItem()
                ->getId()
        );
        return $data;
    }

    /**
     * @depends testEditActionEditDisbursement
     */
    public function testApproveAction(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'approve'],
                'get' => ['id' => $data['settlement_cycle_id']],
            ]
        );
        $this->assertEquals(
            Application_Model_Entity_System_PaymentStatus::APPROVED_STATUS,
            (new Application_Model_Entity_Settlement_Cycle())->load($data['settlement_cycle_id'])
                ->getDisbursementStatus()
        );
    }

    /**
     * @depends testEditActionEditDisbursement
     */
    public function testEditActionApproved(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
    }

    /**
     * @depends testEditActionEditDisbursement
     */
    //    public function testDeleteAction(array $data)
    //    {
    //        $this->baseTestAction(
    //            array(
    //                'params' => array('action' => 'delete'),
    //                'get'   => array ('id' => $data['id']),
    //            )
    //        );
    //        $this->assertNull((new Application_Model_Entity_Transactions_Disbursement())->load($data['id'])->getId());
    //    }

    //    public function testBeforeMultiAction()
    //    {
    //        $data =  array(
    //            'bank_account_history_id' => '1',
    //            'entity_id' =>'1',
    //            'bank_account_id' => self::$bankAccountRecipientId,
    //            'bank_account_id_title' => 'Title',
    //            'settlement_cycle_id' => self::$cycleId,
    //            'process_type' => '1',
    //            'status' => '2');
    //        $disbIds[] =(new Application_Model_Entity_Transactions_Disbursement())->setData($data)->save()->getId();
    //        $disbIds[] =(new Application_Model_Entity_Transactions_Disbursement())->setData($data)->save()->getId();
    //        return $disbIds;
    //    }
    //
    //    /**
    //     * @depends testBeforeMultiAction
    //     */
    //    public function testMultiAction(array $disbIds)
    //    {
    //        $this->baseTestAction(
    //            array(
    //                'params' => array('action' => 'multiaction'),
    //                'post'   => array(
    //                    'action-type' => 'delete',
    //                    'ids' => $disbIds[0] . ',' . $disbIds[1]
    //                ),
    //            )
    //        );
    //        $this->assertNull((new Application_Model_Entity_Transactions_Disbursement())->load($disbIds[0])->getId());
    //        $this->assertNull((new Application_Model_Entity_Transactions_Disbursement())->load($disbIds[1])->getId());
    //    }

    public function testUpdatebacollection()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'updatebacollection'],
                'ajax' => [
                    'entity_id' => '1',
                ],
            ]
        );
    }

    public function testUpdatebacollectionGet()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'updatebacollection'],
                'get' => [],
            ]
        );
    }

    public function testUpdatebasetup()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'updatebasetup'],
                'ajax' => [
                    'baId' => '1',
                ],
            ]
        );
    }

    public function testUpdatebasetupGet()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'updatebasetup'],
                'get' => [],
            ]
        );
    }

    /**
     * @depends testEditActionEditDisbursement
     */
    public function testEditActionListGetNotApproved(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'list'],
                'get' => [
                    'cycle' => $data['settlement_cycle_id'],
                ],
            ]
        );
    }

    /**
     * @depends testEditActionEditDisbursement
     */
    public function testEditActionEditGetNotApproved(array $data)
    {
        (new Application_Model_Entity_Settlement_Cycle())->load($data['settlement_cycle_id'])
            ->setStatusId('2')
            ->save();
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => [
                    'cycle' => $data['settlement_cycle_id'],
                ],
            ]
        );
    }

    // PERMISSIONS
    public static $user;

    public function testBeforePermissionsTest()
    {
        $carrier = $this->newCarrier();
        self::$user = $this->newUser(
            [
                'entity_id' => $carrier->getData('entity_id'),
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
        Application_Model_Entity_Accounts_User::login(self::$user->getId());
        $this->setStorage();
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
            [
                'action' => 'list',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'disbursement_view' => '0',
                ],
                'assert' => ['action' => 'list'],
                'function' => false,
            ],
            [
                'action' => 'new',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'disbursement_view' => '0',
                ],
                'assert' => ['action' => 'edit'],
                'function' => false,
            ],
            [
                'action' => 'approve',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'disbursement_approve' => '0',
                ],
                'assert' => ['action' => 'approve'],
                'function' => false,
            ],
        ];
    }
}
