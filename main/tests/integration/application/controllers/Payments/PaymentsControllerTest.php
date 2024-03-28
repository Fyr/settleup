<?php

class Payments_PaymentsControllerTest extends BaseTestCase
{
    /** @var Payments_PaymentsController */
    private $controller;
    public static $payment_id;

    protected function setUp(): void
    {
        $this->setDefaultController('payments_payments');
        parent::setUp();
    }

    public function testNewActionNewPaymentSetup()
    {
        $carrier = $this->newCarrier();
        $contractor = $this->newContractor($carrier);
        $setup = $this->newPaymentSetup($carrier);
        Application_Model_Entity_Accounts_User::login($this->_myUser);
        $this->setStorage();
        $cycle = $this->newCycle($carrier);
        $cycle->verify();

        $payment_last_id = (new Application_Model_Entity_Payments_Payment())->getCollection()
            ->getLastItem()
            ->getId();
        $data = [
            'selectedSetup' => [$setup->getId()],
            'selectedContractors' => [$contractor->getData('entity_id')],
            'selectedCycle' => $cycle->getId(),
        ];

        $this->baseTestAction(
            [
                'params' => ['action' => 'new'],
                'ajax' => $data,
            ]
        );

        self::$payment_id = (new Application_Model_Entity_Payments_Payment())->getCollection()
            ->getLastItem()
            ->getId();
        $this->assertEquals(
            self::$payment_id,
            $payment_last_id + 1
        );
    }

    public function testIndexAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'index'],
                'get' => [],
                'assert' => [
                    'action' => 'list',
                ],
            ]
        );
    }

    public function testListAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'list'],
                'get' => ['isAjax' => 'true'],
            ]
        );
    }

    /**
     * @depends testNewActionNewPaymentSetup
     */
    public function testEditActionNewPayment()
    {
        $payment = (new Application_Model_Entity_Payments_Payment())->load(self::$payment_id);

        $data = $payment->getData();
        array_walk(
            $data,
            function (&$value) {
                if ($value == null) {
                    $value = '';
                }
            }
        );

        unset($data['settlement_cycle_id']);
        $data['description'] = 'payment EDIT';
        $data['invoice_date'] = (new DateTime($data['invoice_date']))->format('m/d/Y');
        $data['quantity'] = '21';
        $data['rate'] = '12';
        $data['terms'] = '5';
        $data['submit'] = 'Save';

        $payment_last_id = (new Application_Model_Entity_Payments_Payment())->getCollection()
            ->getLastItem()
            ->getId();
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );

        $this->assertEquals(
            (new Application_Model_Entity_Payments_Payment())->getCollection()
                ->getLastItem()
                ->getId(),
            $payment_last_id
        );

        $payment_new_data = (new Application_Model_Entity_Payments_Payment())->load($data['id'])
            ->getData();

        $this->assertEquals(
            [
                $data['description'],
                $data['quantity'],
                $data['rate'],
                $data['terms'],
            ],
            [
                $payment_new_data['description'],
                $payment_new_data['quantity'],
                $payment_new_data['rate'],
                $payment_new_data['terms'],
            ]
        );

        return $data;
    }

    public function testEditActionMethodGet()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => ['id' => self::$payment_id],
            ]
        );
    }

    /**
     * @depends testEditActionNewPayment
     */
    public function testEditActionNotValid(array $data)
    {
        $data['description'] = '';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $this->assertNotEquals(
            $data['description'],
            (new Application_Model_Entity_Payments_Payment())->load($data['id'])
                ->getDescription()
        );
    }

    public function testDeleteAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'delete'],
                'get' => ['id' => self::$payment_id],
            ]
        );
        $this->assertEquals(
            (new Application_Model_Entity_Payments_Payment())->load(self::$payment_id)
                ->getData('deleted'),
            '1'
        );
    }

    public function testDeleteActionWithRedirectBack()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'delete'],
                'get' => [
                    'id' => self::$payment_id,
                    'back' => 'index.index',
                ],
            ]
        );
        $this->assertEquals(
            (new Application_Model_Entity_Payments_Payment())->load(self::$payment_id)
                ->getData('deleted'),
            '1'
        );
    }

    /**
     * @depends testEditActionNewPayment
     */
    public function testAddBeforeMultiAction(array $data)
    {
        $data['description'] = 'PHPUnit compensation MULTI' . random_int(1, 32000);
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $multiId = (new Application_Model_Entity_Payments_Payment())->load($data['description'], 'description')
            ->getId();
        return $multiId;
    }

    /**
     * @depends testEditActionNewPayment
     * @depends testAddBeforeMultiAction
     */
    public function testMultiDeleteAction(array $data, $multiId)
    {
        $this->setFieldValue(
            'payments',
            $data['id'],
            'status',
            Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID
        );
        $this->setFieldValue('payments', $data['id'], 'deleted', '0');

        $this->setFieldValue(
            'payments',
            $multiId,
            'status',
            Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID
        );
        $this->setFieldValue('payments', $multiId, 'deleted', '0');

        $this->baseTestAction(
            [
                'params' => ['action' => 'multiaction'],
                'ajax' => [
                    'action-type' => 'delete',
                    'ids' => $data['id'] . ',' . $multiId,
                ],
            ]
        );
        $this->assertEquals(
            '1',
            (new Application_Model_Entity_Payments_Payment())->load($data['id'])
                ->getDeleted()
        );
        $this->assertEquals(
            '1',
            (new Application_Model_Entity_Payments_Payment())->load($multiId)
                ->getDeleted()
        );
    }

    public function testMultiactionActionWithRedirectBack()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'multiaction'],
                'ajax' => [
                    'action-type' => '',
                    'ids' => self::$payment_id,
                    'back' => 'index.index',
                ],
            ]
        );
    }

    /**
     * @depends testEditActionNewPayment
     */
    public function testEditActionGetApproved(array $data)
    {
        $this->setFieldValue('payments', $data['id'], 'deleted', '0');
        (new Application_Model_Entity_Settlement_Cycle())->load(
            (new Application_Model_Entity_Payments_Payment())->load($data['id'])
                ->getSettlementCycleId()
        )
            ->setData('status_id', Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID)
            ->save();

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => [
                    'id' => $data['id'],
                ],
            ]
        );
    }
}
