<?php

class Deductions_SetupControllerTest extends BaseTestCase
{
    /** @var Deductions_SetupController */
    private $controller;

    protected function setUp(): void
    {
        $this->setDefaultController('deductions_setup');
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
            'provider_id' => $carrier->getData('entity_id'),
            'level_id' => '1',
            'provider_id_title' => 'PHPUnitProvider',
            'vendor_deduction_code' => '',
            'deduction_code' => '',
            'description' => 'PHPUnit deduction setup' . random_int(1, 32000),
            'category' => '',
            'department' => '',
            'gl_code' => '',
            'quantity' => '1',
            'disbursement_code' => '',
            'terms' => '2',
            'recurring' => '0',
            'billing_cycle_id' => Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID,
            'first_start_day' => '',
            'second_start_day' => '',
            'rate' => '5',
            'eligible' => '0',
            'reserve_account_receiver_title' => '',
            'reserve_account_receiver' => '',
            'submit' => 'Save',
        ];
        $setup_last_id = (new Application_Model_Entity_Deductions_Setup())->getCollection()
            ->getLastItem()
            ->getId();
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );

        $this->assertEquals(
            (new Application_Model_Entity_Deductions_Setup())->getCollection()
                ->getLastItem()
                ->getId(),
            $setup_last_id + 1
        );

        $setup = (new Application_Model_Entity_Deductions_Setup())->getCollection()
            ->addFilter('description', $data['description'])
            ->getFirstItem();

        $this->assertNotNull($setup->getId());

        $setup_data = $setup->getData();

        $this->assertEquals(
            [
                empty($setup_data['reserve_account_receiver']),
                empty($setup_data['eligible']),
                empty($setup_data['master_setup_id']),
                empty($setup_data['contractor_id']),
                empty($setup_data['changed']),
            ],
            array_fill(0, 5, true)
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

        $setup_last_id = (new Application_Model_Entity_Deductions_Setup())->getCollection()
            ->getLastItem()
            ->getId();

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $this->assertEquals(
            (new Application_Model_Entity_Deductions_Setup())->getCollection()
                ->getLastItem()
                ->getId(),
            $setup_last_id
        );

        $setup_data = (new Application_Model_Entity_Deductions_Setup())->load($data['id'])
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
    public function testEditActionVendorProvider(array $data)
    {
        $userVendorId = (new Application_Model_Entity_Accounts_User())->getCollection()
            ->addFilter('role_id', Application_Model_Entity_System_UserRoles::VENDOR_ROLE_ID)
            ->getFirstItem()
            ->getId();
        Application_Model_Entity_Accounts_User::login($userVendorId);
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ],
            false
        );
    }

    /**
     * @depends testEditActionEditSetup
     */
    public function testEditActionNotValid(array $data)
    {
        $setup = new Application_Model_Entity_Deductions_Setup();
        $setupIdBefore = $setup->getCollection()
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
            (new Application_Model_Entity_Deductions_Setup())->getCollection()
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
            (new Application_Model_Entity_Deductions_Setup())->load($data['id'])
                ->getData('deleted'),
            '1'
        );
    }

    /**
     * @depends testEditActionNewSetup
     */
    public function testAddBeforeMultiAction(array $data)
    {
        $data['description'] = 'PHPUnit deduction setup MULTI' . random_int(1, 32000);
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $multiId = (new Application_Model_Entity_Deductions_Setup())->load($data['description'], 'description')
            ->getId();
        return $multiId;
    }

    /**
     * @depends testEditActionEditSetup
     * @depends testAddBeforeMultiAction
     */
    public function testMultiAction(array $data, $multiId)
    {
        (new Application_Model_Entity_Deductions_Setup())->load($data['id'])
            ->setDeleted('0')
            ->save();
        (new Application_Model_Entity_Deductions_Setup())->load($multiId)
            ->setDeleted('0')
            ->save();
        $this->baseTestAction(
            [
                'params' => ['action' => 'multiaction'],
                'ajax' => ['ids' => $data['id'] . ',' . $multiId],
            ]
        );
        $this->assertEquals(
            '1',
            (new Application_Model_Entity_Deductions_Setup())->load($data['id'])
                ->getDeleted()
        );
        $this->assertEquals(
            '1',
            (new Application_Model_Entity_Deductions_Setup())->load($multiId)
                ->getDeleted()
        );
    }

    public function testUpdateRaCollectionActionAjax()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'updateracollection'],
                'ajax' => [
                    'vendorEntityId' => (new Application_Model_Entity_Entity_Vendor())->getCollection()
                        ->getFirstItem()
                        ->getEntityId(),
                ],
            ]
        );
    }

    public function testUpdateRaCollectionActionGet()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'updateracollection'],
                'get' => [],
            ]
        );
    }

    public function testUpdateTermsActionAjax()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'updateterms'],
                'ajax' => [
                    'providerId' => (new Application_Model_Entity_Entity_Carrier())->getCollection()
                        ->getFirstItem()
                        ->getData('entity_id'),
                ],
            ]
        );
    }

    public function testUpdateTermsActionGet()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'updateterms'],
                'get' => [],
            ]
        );
    }

    //@TODO test create contractor after creating Template

    public function testEditActionNewIndividualLevelTemplate()
    {
        $carrier = $this->newCarrier();
        $contractor = $this->newContractor($carrier);
        $carrierRA = $this->newReserveAccount($carrier);

        $data = [
            'id' => '',
            'provider_id' => $carrier->getData('entity_id'),
            'level_id' => Application_Model_Entity_System_SetupLevels::MASTER_LEVEL_ID,
            'provider_id_title' => 'PHPUnitProvider',
            'vendor_deduction_code' => '',
            'deduction_code' => '',
            'description' => 'Individual' . random_int(1, 32000),
            'category' => '',
            'department' => '',
            'gl_code' => '',
            'quantity' => '1',
            'disbursement_code' => '',
            'terms' => '2',
            'recurring' => '0',
            'billing_cycle_id' => Application_Model_Entity_System_CyclePeriod::WEEKLY_PERIOD_ID,
            'first_start_day' => '',
            'second_start_day' => '',
            'rate' => '5',
            'eligible' => '0',
            'reserve_account_receiver_title' => '',
            'reserve_account_receiver' => '',
            'submit' => 'Save',
        ];

        $setup_last_id = (new Application_Model_Entity_Deductions_Setup())->getCollection()
            ->getLastItem()
            ->getId();
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );

        $this->assertEquals(
            (new Application_Model_Entity_Deductions_Setup())->getCollection()
                ->getLastItem()
                ->getId(),
            $setup_last_id + 2
        );

        $masterSetup = (new Application_Model_Entity_Deductions_Setup())->getCollection()
            ->addFilter('description', $data['description'])
            ->addFilter('level_id', Application_Model_Entity_System_SetupLevels::MASTER_LEVEL_ID)
            ->getFirstItem();

        $this->assertEquals(
            [
                $masterSetup->getData('provider_id'),
            ],
            [
                $data['provider_id'],
            ]
        );

        $this->assertNotNull($masterSetup->getId());

        $individualSetup = (new Application_Model_Entity_Deductions_Setup())->getCollection()
            ->addFilter('master_setup_id', $masterSetup->getId())
            ->getFirstItem();

        $this->assertNotNull($individualSetup->getId());

        $individual_setup_data = $individualSetup->getData();

        $this->assertEquals(
            [
                $individual_setup_data['contractor_id'],
                empty($setup_data['changed']),
                $individual_setup_data['provider_id'],
            ],
            [
                $contractor->getData('entity_id'),
                true,
                $data['provider_id'],
            ]
        );

        $data['master_id'] = $masterSetup->getId();
        $data['individual_id'] = $individualSetup->getId();
        $data['receiver_id'] = $carrierRA->getData('reserve_account_id');
        return $data;
    }

    /**
     * @depends  testEditActionNewIndividualLevelTemplate
     */
    public function testEditActionEditMasterSetup(array $data)
    {
        $master_data = (new Application_Model_Entity_Deductions_Setup())->load($data['master_id'])
            ->getData();
        $individual_data = (new Application_Model_Entity_Deductions_Setup())->load($data['individual_id'])
            ->getData();
        $reserve_account_id = $data['receiver_id'];

        unset($data['master_id']);
        unset($data['individual_id']);

        $data['eligible'] = '1';
        $data['reserve_account_receiver'] = $reserve_account_id;
        $data['reserve_account_receiver_title'] = 'Carrier Res Account';
        $data['description'] = $master_data['description'] . 'EDIT';
        $data['terms'] = '20';
        $data['recurring'] = '1';
        $data['billing_cycle_id'] = Application_Model_Entity_System_CyclePeriod::SEMI_WEEKLY_PERIOD_ID;
        $data['first_start_day'] = '1';
        $data['second_start_day'] = '1';
        $data['week_day'] = '1';
        $data['second_week_day'] = '4';

        $setup_last_id = (new Application_Model_Entity_Deductions_Setup())->getCollection()
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
            (new Application_Model_Entity_Deductions_Setup())->getCollection()
                ->getLastItem()
                ->getId(),
            $setup_last_id
        );

        $master_data_new = (new Application_Model_Entity_Deductions_Setup())->load($master_data['id'])
            ->getData();
        $individual_data_new = (new Application_Model_Entity_Deductions_Setup())->load($individual_data['id'])
            ->getData();

        $actual_data = [
            $data['eligible'],
            $data['reserve_account_receiver'],
            $data['description'],
            $data['terms'],
            $data['recurring'],
            $data['billing_cycle_id'],
            $data['week_day'],
            $data['second_week_day'],
        ];

        $this->assertEquals(
            [
                $master_data_new['eligible'],
                $master_data_new['reserve_account_receiver'],
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
                $individual_data_new['eligible'],
                $individual_data_new['reserve_account_receiver'],
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
        $master_data = (new Application_Model_Entity_Deductions_Setup())->load($data['master_id'])
            ->getData();
        $individual_data = (new Application_Model_Entity_Deductions_Setup())->load($data['individual_id'])
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
        $data['reserve_account_receiver_title'] = 'Carrier Res Account Twice';

        $setup_last_id = (new Application_Model_Entity_Deductions_Setup())->getCollection()
            ->getLastItem()
            ->getId();

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );

        $this->assertEquals(
            (new Application_Model_Entity_Deductions_Setup())->getCollection()
                ->getLastItem()
                ->getId(),
            $setup_last_id
        );

        $master_data_new = (new Application_Model_Entity_Deductions_Setup())->load($master_data['id'])
            ->getData();
        $individual_data_new = (new Application_Model_Entity_Deductions_Setup())->load($individual_data['id'])
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
        $master_data = (new Application_Model_Entity_Deductions_Setup())->load($data['master_id'])
            ->getData();
        $individual_data = (new Application_Model_Entity_Deductions_Setup())->load($data['individual_id'])
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
        $master_data = (new Application_Model_Entity_Deductions_Setup())->load($data['master_id'])
            ->getData();
        $individual_data = (new Application_Model_Entity_Deductions_Setup())->load($data['individual_id'])
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
                    'provider_id' => '1',
                    'level_id' => '1',
                    'provider_id_title' => 'PHPUnitProvider',
                    'vendor_deduction_code' => '',
                    'deduction_code' => '',
                    'description' => 'no permission',
                    'category' => '',
                    'department' => '',
                    'gl_code' => '',
                    'quantity' => '1',
                    'disbursement_code' => '',
                    'terms' => '2',
                    'recurring' => '0',
                    'billing_cycle_id' => '1',
                    'first_start_day' => '',
                    'second_start_day' => '',
                    'rate' => '5',
                    'eligible' => '0',
                    'reserve_account_receiver_title' => '',
                    'reserve_account_receiver' => '',
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
