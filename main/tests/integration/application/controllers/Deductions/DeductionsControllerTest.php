<?php

class Deductions_DeductionsControllerTest extends BaseTestCase
{
    /** @var Deductions_DeductionsController */
    private $controller;

    protected function setUp(): void
    {
        $this->setDefaultController('deductions_deductions');
        parent::setUp();
    }

    public static $vendor;
    public static $deduction_id;

    public function testNewActionNewDeduction()
    {
        $carrier = $this->newCarrier();
        $contractor = $this->newContractor($carrier);
        $setup = $this->newDeductionSetup($carrier);
        $cycle = $this->newCycle($carrier);
        Application_Model_Entity_Accounts_User::login($this->_myUser);
        $this->setStorage();
        $cycle->verify();

        $ajax = [
            'selectedSetup' => [$setup->getId()],
            'selectedContractors' => [$contractor->getData('entity_id')],
            'selectedCycle' => $cycle->getId(),
            'fromPopup' => 'true',
            'invoiceDate' => '',
        ];

        $deduction_last_id = (new Application_Model_Entity_Deductions_Deduction())->getCollection()
            ->getLastItem()
            ->getId();
        $this->baseTestAction(
            [
                'params' => ['action' => 'new'],
                'ajax' => $ajax,
            ]
        );
        self::$deduction_id = (new Application_Model_Entity_Deductions_Deduction())->getCollection()
            ->getLastItem()
            ->getId();

        $this->assertEquals(
            self::$deduction_id,
            $deduction_last_id + 1
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

    public function testIndexAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'index'],
                'assert' => ['action' => 'list'],
            ]
        );
    }

    //    /**
    //     * @depends testNewActionNewDeduction
    //     */
    //    public function testEditActionNewDeductionSetupMessagesPost(array $data)
    //    {
    //        $carrierId = (new Application_Model_Entity_Accounts_User())->load(16)->getData('last_selected_carrier');
    //        $carrier = (new Application_Model_Entity_Entity_Carrier())->load($carrierId);
    //        $vendor = $this->newVendor($carrier);
    //        $setup = $this->newDeductionSetup($vendor);
    //        $post['selectedSetup'] = array($setup->getId());
    //        $post['back'] = 'index.index';
    //
    //        $lastDeductionId =(new Application_Model_Entity_Deductions_Deduction())->getCollection()->getLastItem()->getId();
    //        $this->baseTestAction(
    //            array(
    //                'params' => array('action' => 'new'),
    //                'post' => $post
    //            )
    //        );
    //        $this->assertEquals($lastDeductionId,
    //            (new Application_Model_Entity_Deductions_Deduction())->getCollection()->getLastItem()->getId()
    //        );
    //    }

    public function testEditActionNewDeduction()
    {
        $deduction = (new Application_Model_Entity_Deductions_Deduction())->load(self::$deduction_id);

        $data = $deduction->getData();
        array_walk(
            $data,
            function (&$value) {
                if ($value == null) {
                    $value = '';
                }
            }
        );

        unset($data['settlement_cycle_id']);
        $data['description'] = 'compensation EDIT';
        $data['invoice_date'] = (new DateTime($data['invoice_date']))->format('m/d/Y');
        $data['quantity'] = '12';
        $data['rate'] = '21';
        $data['terms'] = '4';
        $data['submit'] = 'Save';

        $deduction_last_id = (new Application_Model_Entity_Deductions_Deduction())->getCollection()
            ->getLastItem()
            ->getId();

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );

        $this->assertEquals(
            (new Application_Model_Entity_Deductions_Deduction())->getCollection()
                ->getLastItem()
                ->getId(),
            $deduction_last_id
        );

        $deduction_new_data = (new Application_Model_Entity_Deductions_Deduction())->load($data['id'])
            ->getData();

        $this->assertEquals(
            [
                $data['description'],
                $data['quantity'],
                $data['rate'],
                $data['terms'],
            ],
            [
                $deduction_new_data['description'],
                $deduction_new_data['quantity'],
                $deduction_new_data['rate'],
                $deduction_new_data['terms'],
            ]
        );

        return $data;
    }

    public function testEditAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => ['id' => self::$deduction_id],
            ]
        );
    }

    public function testEditActionNotValid()
    {
        $data = (new Application_Model_Entity_Deductions_Deduction())->load(self::$deduction_id)
            ->getData();
        array_walk(
            $data,
            function (&$value) {
                if ($value == null) {
                    $value = '';
                }
            }
        );
        $data['description'] = '';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $this->assertNotEquals(
            $data['description'],
            (new Application_Model_Entity_Deductions_Deduction())->load($data['id'])
                ->getDescription()
        );
    }

    public function testDeleteAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'delete'],
                'get' => ['id' => self::$deduction_id],
            ]
        );
        $this->assertEquals(
            (new Application_Model_Entity_Deductions_Deduction())->load(self::$deduction_id)
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
                    'id' => self::$deduction_id,
                    'back' => 'index.index',
                ],
            ]
        );
        $this->assertEquals(
            (new Application_Model_Entity_Deductions_Deduction())->load(self::$deduction_id)
                ->getData('deleted'),
            '1'
        );
    }

    /**
     * @depends testEditActionNewDeduction
     */
    public function testAddBeforeMultiAction(array $data)
    {
        $data['description'] = 'PHPUnit deduction MULTI' . random_int(1, 32000);
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $multiId = (new Application_Model_Entity_Deductions_Deduction())->load($data['description'], 'description')
            ->getId();
        return $multiId;
    }

    /**
     * @depends testEditActionNewDeduction
     * @depends testAddBeforeMultiAction
     */
    public function testMultiactionDeleteAction(array $data, $multiId)
    {
        $this->setFieldValue(
            'deductions',
            $data['id'],
            'status',
            Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID
        );
        $this->setFieldValue('deductions', $data['id'], 'deleted', '0');

        $this->setFieldValue(
            'deductions',
            $multiId,
            'status',
            Application_Model_Entity_System_SettlementCycleStatus::PROCESSED_STATUS_ID
        );
        $this->setFieldValue('deductions', $multiId, 'deleted', '0');

        $this->baseTestAction(
            [
                'params' => ['action' => 'multiaction'],
                'ajax' => [
                    'action-type' => 'delete',
                    'ids' => $data['id'] . ',' . $multiId,
                    'all' => false,
                ],
            ]
        );
        $this->assertEquals(
            '1',
            (new Application_Model_Entity_Deductions_Deduction())->load($data['id'])
                ->getDeleted()
        );
        $this->assertEquals(
            '1',
            (new Application_Model_Entity_Deductions_Deduction())->load($multiId)
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
                    'ids' => '',
                    'back' => 'index.index',
                ],
            ]
        );
    }

    public function testEditActionGetApproved()
    {
        $this->setFieldValue('deductions', self::$deduction_id, 'deleted', '0');
        (new Application_Model_Entity_Settlement_Cycle())->load(
            (new Application_Model_Entity_Deductions_Deduction())->load(self::$deduction_id)
                ->getSettlementCycleId()
        )
            ->setData('status_id', Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID)
            ->save();

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => [
                    'id' => self::$deduction_id,
                ],
            ]
        );
    }
}
