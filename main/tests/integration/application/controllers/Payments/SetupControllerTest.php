<?php

class Payments_SetupControllerTest extends BaseTestCase
{
    /** @var Payments_SetupController */
    private $controller;

    protected function setUp(): void
    {
        $this->setDefaultController('payments_setup');
        parent::setUp();
    }

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

    public function testNewAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'new'],
                'assert' => ['action' => 'edit'],
            ]
        );
    }

    public function testEditActionNewSetup()
    {
        $carrier = $this->newCarrier();
        $data = [
            'id' => '',
            'level_id' => '1',
            'carrier_id' => $carrier->getData('entity_id'),
            'payment_code' => '',
            'carrier_payment_code' => '',
            'description' => 'PHPUnit compensation setup' . random_int(1, 32000),
            'category' => '',
            'terms' => '1',
            'department' => '',
            'gl_code' => '',
            'quantity' => '2',
            'disbursement_code' => '',
            'recurring' => '0',
            'billing_cycle_id' => Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID,
            'first_start_day' => '1',
            'second_start_day' => '1',
            'rate' => '3',
            'submit' => 'Save',
        ];

        $setup_last_id = (new Application_Model_Entity_Payments_Setup())->getCollection()
            ->getLastItem()
            ->getId();
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );

        $this->assertEquals(
            (new Application_Model_Entity_Payments_Setup())->getCollection()
                ->getLastItem()
                ->getId(),
            $setup_last_id + 1
        );

        $setup = (new Application_Model_Entity_Payments_Setup())->getCollection()
            ->addFilter('description', $data['description'])
            ->getFirstItem();

        $this->assertNotNull($setup->getId());

        $setup_data = $setup->getData();

        $this->assertEquals(
            [
                empty($setup_data['recurring']),
                empty($setup_data['master_setup_id']),
                empty($setup_data['contractor_id']),
                empty($setup_data['changed']),
            ],
            array_fill(0, 4, true)
        );

        $data['id'] = $setup->getId();
        return $data;
    }

    /**
     * @depends testEditActionNewSetup
     */
    public function testEditActionEditSetup(array $data)
    {
        $data['description'] = $data['description'] . 'EDIT';
        $data['recurring'] = '1';
        $data['billing_cycle_id'] = Application_Model_Entity_System_CyclePeriod::MONTHLY_PERIOD_ID;
        $data['first_start_day'] = '1';

        $setup_last_id = (new Application_Model_Entity_Payments_Setup())->getCollection()
            ->getLastItem()
            ->getId();

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $this->assertEquals(
            (new Application_Model_Entity_Payments_Setup())->getCollection()
                ->getLastItem()
                ->getId(),
            $setup_last_id
        );

        $setup_data = (new Application_Model_Entity_Payments_Setup())->load($data['id'])
            ->getData();

        $this->assertEquals(
            [
                $setup_data['description'],
                $setup_data['recurring'],
                $setup_data['billing_cycle_id'],
                $setup_data['first_start_day'],
            ],
            [
                $data['description'],
                $data['recurring'],
                $data['billing_cycle_id'],
                $data['first_start_day'],
            ]
        );
        return $data;
    }

    /**
     * @depends testEditActionEditSetup
     */
    public function testEditActionMethodGet(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => ['id' => $data['id']],
            ]
        );
    }

    /**
     * @depends testEditActionEditSetup
     */
    public function testEditActionEditSetupSemiMonthly(array $data)
    {
        $data['billing_cycle_id'] = Application_Model_Entity_System_CyclePeriod::SEMY_MONTHLY_PERIOD_ID;
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
    }

    /**
     * @depends testEditActionEditSetup
     */
    public function testEditActionNotValid(array $data)
    {
        $setupIdBefore = (new Application_Model_Entity_Payments_Setup())->getCollection()
            ->getLastItem()
            ->getId();
        $data['description'] = '';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $this->assertEquals(
            $setupIdBefore,
            (new Application_Model_Entity_Payments_Setup())->getCollection()
                ->getLastItem()
                ->getId()
        );
    }

    /**
     * @depends testEditActionEditSetup
     */
    public function testDeleteAction(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'delete'],
                'get' => ['id' => $data['id']],
            ]
        );
        $this->assertEquals(
            (new Application_Model_Entity_Payments_Setup())->load($data['id'])
                ->getData('deleted'),
            '1'
        );
    }

    /**
     * @depends testEditActionEditSetup
     */
    public function testAddBeforeMultiAction(array $data)
    {
        $data['description'] = 'PHPUnit compensation setup MULTI' . random_int(1, 32000);
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $multi = (new Application_Model_Entity_Payments_Setup())->load($data['description'], 'description')
            ->getId();
        return $multi;
    }

    /**
     * @depends testEditActionEditSetup
     * @depends testAddBeforeMultiAction
     */
    public function testMultiAction(array $data, $multi)
    {
        (new Application_Model_Entity_Payments_Setup())->load($data['id'])
            ->setDeleted('0')
            ->save();
        (new Application_Model_Entity_Payments_Setup())->load($multi)
            ->setDeleted('0')
            ->save();
        $this->baseTestAction(
            [
                'params' => ['action' => 'multiaction'],
                'ajax' => ['ids' => $data['id'] . ',' . $multi],
            ]
        );
        $this->assertEquals(
            '1',
            (new Application_Model_Entity_Payments_Setup())->load($data['id'])
                ->getDeleted()
        );
        $this->assertEquals(
            '1',
            (new Application_Model_Entity_Payments_Setup())->load($multi)
                ->getDeleted()
        );
    }

    //@TODO test create contractor after creating Template

    public function testEditActionNewIndividualLevelTemplate()
    {
        $carrier = $this->newCarrier();
        $contractor = $this->newContractor($carrier);

        $data = [
            'id' => '',
            'level_id' => Application_Model_Entity_System_SetupLevels::MASTER_LEVEL_ID,
            'carrier_id' => $carrier->getData('entity_id'),
            'payment_code' => '',
            'carrier_payment_code' => '',
            'description' => 'Individual' . random_int(1, 32000),
            'category' => '',
            'terms' => '1',
            'department' => '',
            'gl_code' => '',
            'quantity' => '2',
            'disbursement_code' => '',
            'recurring' => '0',
            'billing_cycle_id' => Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID,
            'first_start_day' => '1',
            'second_start_day' => '1',
            'rate' => '3',
            'submit' => 'Save',
        ];

        $setup_last_id = (new Application_Model_Entity_Payments_Setup())->getCollection()
            ->getLastItem()
            ->getId();
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );

        $this->assertEquals(
            (new Application_Model_Entity_Payments_Setup())->getCollection()
                ->getLastItem()
                ->getId(),
            $setup_last_id + 2
        );

        $masterSetup = (new Application_Model_Entity_Payments_Setup())->getCollection()
            ->addFilter('description', $data['description'])
            ->addFilter('level_id', Application_Model_Entity_System_SetupLevels::MASTER_LEVEL_ID)
            ->getFirstItem();

        $this->assertEquals(
            [
                $masterSetup->getData('carrier_id'),
            ],
            [
                $data['carrier_id'],
            ]
        );

        $this->assertNotNull($masterSetup->getId());

        $individualSetup = (new Application_Model_Entity_Payments_Setup())->getCollection()
            ->addFilter('master_setup_id', $masterSetup->getId())
            ->getFirstItem();

        $this->assertNotNull($individualSetup->getId());

        $individual_setup_data = $individualSetup->getData();

        $this->assertEquals(
            [
                $individual_setup_data['contractor_id'],
                empty($setup_data['changed']),
            ],
            [
                $contractor->getData('entity_id'),
                true,
            ]
        );

        $data['master_id'] = $masterSetup->getId();
        $data['individual_id'] = $individualSetup->getId();
        return $data;
    }

    /**
     * @depends  testEditActionNewIndividualLevelTemplate
     */
    public function testEditActionEditMasterSetup(array $data)
    {
        $master_data = (new Application_Model_Entity_Payments_Setup())->load($data['master_id'])
            ->getData();
        $individual_data = (new Application_Model_Entity_Payments_Setup())->load($data['individual_id'])
            ->getData();

        unset($data['master_id']);
        unset($data['individual_id']);

        $data['description'] = $master_data['description'] . 'EDIT';
        $data['terms'] = '20';
        $data['recurring'] = '1';
        $data['billing_cycle_id'] = Application_Model_Entity_System_CyclePeriod::SEMI_WEEKLY_PERIOD_ID;
        $data['first_start_day'] = '1';
        $data['second_start_day'] = '1';
        $data['week_day'] = '1';
        $data['second_week_day'] = '4';

        $setup_last_id = (new Application_Model_Entity_Payments_Setup())->getCollection()
            ->getLastItem()
            ->getId();

        $data['id'] = $master_data['id'];
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );

        $this->assertEquals(
            (new Application_Model_Entity_Payments_Setup())->getCollection()
                ->getLastItem()
                ->getId(),
            $setup_last_id
        );

        $master_data_new = (new Application_Model_Entity_Payments_Setup())->load($master_data['id'])
            ->getData();
        $individual_data_new = (new Application_Model_Entity_Payments_Setup())->load($individual_data['id'])
            ->getData();

        $actual_data = [
            $data['description'],
            $data['terms'],
            $data['recurring'],
            $data['billing_cycle_id'],
            $data['week_day'],
            $data['second_week_day'],
        ];

        $this->assertEquals(
            [
                $master_data_new['description'],
                $master_data_new['terms'],
                $master_data_new['recurring'],
                $master_data_new['billing_cycle_id'],
                $master_data_new['first_start_day'],
                $master_data_new['second_start_day'],
            ],
            $actual_data
        );

        $this->assertEquals(
            [
                $individual_data_new['description'],
                $individual_data_new['terms'],
                $individual_data_new['recurring'],
                $individual_data_new['billing_cycle_id'],
                $individual_data_new['first_start_day'],
                $individual_data_new['second_start_day'],
            ],
            $actual_data
        );
    }

    /**
     * @depends  testEditActionNewIndividualLevelTemplate
     */
    public function testEditActionEditIndividualSetup(array $data)
    {
        $master_data = (new Application_Model_Entity_Payments_Setup())->load($data['master_id'])
            ->getData();
        $individual_data = (new Application_Model_Entity_Payments_Setup())->load($data['individual_id'])
            ->getData();

        unset($data['master_id']);
        unset($data['individual_id']);

        $data = $individual_data;
        array_walk(
            $data,
            function (&$value) {
                if ($value == null) {
                    $value = '';
                }
            }
        );

        $data['id'] = $individual_data['id'];
        $data['description'] = $individual_data['description'] . 'TWICE';
        $data['quantity'] = '19';
        $data['rate'] = '5';
        $data['recurring'] = '1';
        $data['billing_cycle_id'] = Application_Model_Entity_System_CyclePeriod::MONTHLY_PERIOD_ID;
        $data['first_start_day'] = '15';
        $data['second_start_day'] = '';

        $setup_last_id = (new Application_Model_Entity_Payments_Setup())->getCollection()
            ->getLastItem()
            ->getId();

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );

        $this->assertEquals(
            (new Application_Model_Entity_Payments_Setup())->getCollection()
                ->getLastItem()
                ->getId(),
            $setup_last_id
        );

        $master_data_new = (new Application_Model_Entity_Payments_Setup())->load($master_data['id'])
            ->getData();
        $individual_data_new = (new Application_Model_Entity_Payments_Setup())->load($individual_data['id'])
            ->getData();

        $actual_data = [
            $data['description'],
            $data['quantity'],
            $data['rate'],
            $data['recurring'],
            $data['billing_cycle_id'],
            $data['first_start_day'],
        ];

        $this->assertEquals(
            [
                $individual_data_new['description'],
                $individual_data_new['quantity'],
                $individual_data_new['rate'],
                $individual_data_new['recurring'],
                $individual_data_new['billing_cycle_id'],
                $individual_data_new['first_start_day'],
            ],
            $actual_data
        );
        $this->assertEquals($individual_data_new['changed'], '1');

        $this->assertEquals(
            $master_data,
            $master_data_new
        );
    }

    /**
     * @depends  testEditActionNewIndividualLevelTemplate
     */
    public function testDeleteActionIndividualSetup(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'delete'],
                'get' => ['id' => $data['individual_id']],
            ]
        );
        $master_data = (new Application_Model_Entity_Payments_Setup())->load($data['master_id'])
            ->getData();
        $individual_data = (new Application_Model_Entity_Payments_Setup())->load($data['individual_id'])
            ->getData();
        $this->assertEquals($master_data['deleted'], '0');
        $this->assertEquals($individual_data['deleted'], '0');
    }

    /**
     * @depends  testEditActionNewIndividualLevelTemplate
     */
    public function testDeleteActionMasterSetup(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'delete'],
                'get' => ['id' => $data['master_id']],
            ]
        );
        $master_data = (new Application_Model_Entity_Payments_Setup())->load($data['master_id'])
            ->getData();
        $individual_data = (new Application_Model_Entity_Payments_Setup())->load($data['individual_id'])
            ->getData();
        $this->assertEquals($master_data['deleted'], '1');
        $this->assertEquals($individual_data['deleted'], '1');
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
                'action' => 'index',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'template_view' => '0',
                ],
                'assert' => ['action' => 'index'],
                'function' => false,
            ],
            [
                'action' => 'new',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'template_view' => '0',
                ],
                'assert' => ['action' => 'edit'],
                'function' => false,
            ],
            [
                'action' => 'edit',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'template_view' => '0',
                ],
                'assert' => ['action' => 'edit'],
                'function' => false,
            ],
            [
                'action' => 'edit',
                'method' => 'post',
                'method_params' => [
                    'id' => '',
                    'level_id' => '1',
                    'carrier_id' => '1',
                    'payment_code' => '',
                    'carrier_payment_code' => '',
                    'description' => 'no permission',
                    'category' => '',
                    'terms' => '1',
                    'department' => '',
                    'gl_code' => '',
                    'quantity' => '2',
                    'disbursement_code' => '',
                    'recurring' => '0',
                    'billing_cycle_id' => '1',
                    'first_start_day' => '1',
                    'second_start_day' => '1',
                    'rate' => '3',
                    'submit' => 'Save',
                ],
                'permissions' => [
                    'template_view' => '1',
                    'template_manage' => '0',
                ],
                'assert' => ['action' => 'edit'],
                'function' => false,
            ],
            [
                'action' => 'edit',
                'method' => 'get',
                'method_params' => ['id' => '1'],
                'permissions' => [
                    'template_view' => '0',
                ],
                'assert' => ['action' => 'edit'],
                'function' => false,
            ],
            [
                'action' => 'list',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'template_view' => '0',
                ],
                'assert' => ['action' => 'list'],
                'function' => false,
            ],
            [
                'action' => 'delete',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'template_manage' => '0',
                ],
                'assert' => ['action' => 'delete'],
                'function' => false,
            ],
            [
                'action' => 'multiaction',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'template_manage' => '0',
                ],
                'assert' => ['action' => 'multiaction'],
                'function' => false,
            ],
        ];
    }
}
