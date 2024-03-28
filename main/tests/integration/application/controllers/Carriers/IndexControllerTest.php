<?php

class Carriers_IndexControllerTest extends BaseTestCase
{
    /** @var Carriers_IndexController */
    private $controller;
    public static $carrier_id;

    protected function setUp(): void
    {
        $this->setDefaultController('carriers_index');
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

    public function testListActionCarrier()
    {
        $userCarrierId = $this->getUserByRole('2')
            ->getId();
        Application_Model_Entity_Accounts_User::login($userCarrierId);
        $this->setStorage();
        $this->baseTestAction(
            [
                'params' => ['action' => 'list'],
            ],
            false
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

    public function testInfoAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'info'],
                'assert' => ['action' => 'edit'],
            ]
        );
    }

    public function testEditActionNewCarrier()
    {
        $post = [
            'id' => '',
            'entity_id' => '',
            'tax_id' => '11-1' . random_int(100, 999) . random_int(100, 999),
            'short_code' => random_int(1, 32000),
            'name' => 'PHPUnit carrier POST' . random_int(1, 32000),
            'contact' => '1',
            'terms' => '1',
            'create_contractor_type' => 1,
            'contacts' => [
                '1000' => [
                    'id' => '',
                    'entity_id' => '',
                    'contact_type' => '1',
                    'title' => 'Address',
                    'deleted' => '',
                    'value' => '{"address":"Pozharnyy lane","city":"","state":"","zip":""}',
                ],
                '1001' => [
                    'id' => '',
                    'entity_id' => '',
                    'contact_type' => '5',
                    'title' => 'Phone',
                    'deleted' => '',
                    'value' => '',
                ],
                '1002' => [
                    'id' => '',
                    'entity_id' => '',
                    'contact_type' => '9',
                    'title' => 'Fax',
                    'deleted' => '',
                    'value' => '',
                ],
                '1003' => [
                    'id' => '',
                    'entity_id' => '',
                    'contact_type' => '8',
                    'title' => 'Email',
                    'deleted' => '',
                    'value' => '',
                ],
            ],
            'address' => 'Pozharnyy lane',
            'city' => '',
            'state' => '',
            'zip' => '',
            'correspondence_method' => '1',
            'vendor' => [
                '1000' => [
                    'id' => '',
                    'deleted' => '0',
                    'vendor_id' => '0',
                    'status' => '0',
                ],
            ],
            'submit' => 'Save',
        ];

        $last_carrier_id = (new Application_Model_Entity_Entity_Carrier())->getCollection()
            ->getLastItem()
            ->getId();

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $post,
            ]
        );

        self::$carrier_id = (new Application_Model_Entity_Entity_Carrier())->getCollection()
            ->getLastItem()
            ->getId();
        $this->assertEquals(
            self::$carrier_id,
            $last_carrier_id + 1
        );
        return $post;
    }

    /**
     * @depends testEditActionNewCarrier
     */
    public function testEditActionEditCarrier(array $data)
    {
        $carrier = (new Application_Model_Entity_Entity_Carrier())->load($data['name'], 'name');
        $data['id'] = $carrier->getId();
        $this->assertNotNull($data['id']);
        $data['entity_id'] = $carrier->getEntity()
            ->getId();
        $data['name'] = $data['name'] . 'EDIT';
        $data['redirect'] = 'index';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $this->assertEquals(
            (new Application_Model_Entity_Entity_Carrier())->load($data['name'], 'name')
                ->getId(),
            $data['id']
        );
        return $data;
    }

    /**
     * @depends testEditActionEditCarrier
     */
    public function testEditAction(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => ['id' => $data['id']],
            ]
        );
    }

    /**
     * @depends testEditActionEditCarrier
     */
    public function testEditActionNotValid(array $data)
    {
        $data['tax_id'] = '';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $this->assertNotEquals(
            (new Application_Model_Entity_Entity_Carrier())->load($data['id'])
                ->getTaxId(),
            $data['tax_id']
        );
    }

    public function testEscrowActionNewEscrowNotValid()
    {
        $data = [
            'id' => '',
            'carrier_id' => self::$carrier_id,
            'escrow_account_holder' => '',
            'holder_federal_tax_id' => '',
            'bank_name' => '',
            'bank_routing_number' => '',
            'bank_account_number' => '',
            'submit' => 'Save',
        ];
        $last_escrow_id = (new Application_Model_Entity_Accounts_Escrow())->getCollection()
            ->getLastItem()
            ->getId();
        $this->baseTestAction(
            [
                'params' => ['action' => 'escrow'],
                'post' => $data,
            ]
        );
        $this->assertEquals(
            (new Application_Model_Entity_Accounts_Escrow())->getCollection()
                ->getLastItem()
                ->getId(),
            $last_escrow_id
        );
    }

    public function testEscrowActionNewEscrow()
    {
        $rnd = $this->getRandomString(10);
        $data = [
            'id' => '',
            'carrier_id' => (new Application_Model_Entity_Entity_Carrier())->load(self::$carrier_id)
                ->getData('entity_id'),
            'escrow_account_holder' => $rnd,
            'holder_federal_tax_id' => random_int(10, 99) . '-' . random_int(1_000_000, 9_999_999),
            'bank_name' => $rnd,
            'bank_routing_number' => '123456' . random_int(100, 999),
            'bank_account_number' => '123456' . random_int(100, 999),
            'holder_address' => $rnd,
            'holder_address_2' => $rnd,
            'holder_city' => $rnd,
            'holder_state' => 'CA',
            'holder_zip' => '12-12345',
            'submit' => 'Save',
        ];
        $last_escrow_id = (new Application_Model_Entity_Accounts_Escrow())->getCollection()
            ->getLastItem()
            ->getId();
        $this->baseTestAction(
            [
                'params' => ['action' => 'escrow'],
                'post' => $data,
            ]
        );
        $this->assertEquals(
            (new Application_Model_Entity_Accounts_Escrow())->getCollection()
                ->getLastItem()
                ->getId(),
            $last_escrow_id + 1
        );
    }

    /**
     * @depends testEditActionEditCarrier
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
            (new Application_Model_Entity_Entity_Carrier())->load($data['id'])
                ->getEntity()
                ->getDeleted(),
            '1'
        );
    }

    /**
     * @depends testEditActionNewCarrier
     */
    public function testAddBeforeMultiAction(array $data)
    {
        $data['name'] = 'PHPUnit carrier MULTI' . random_int(1, 32000);
        $data['tax_id'] = random_int(10, 99) . '-' . random_int(1_000_000, 9_999_999);
        $data['short_code'] = random_int(1, 32000);
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $multiId = (new Application_Model_Entity_Entity_Carrier())->load($data['name'], 'name')
            ->getId();
        return $multiId;
    }

    /**
     * @depends testEditActionEditCarrier
     * @depends testAddBeforeMultiAction
     */
    public function testMultiAction(array $data, $multiId)
    {
        (new Application_Model_Entity_Entity_Carrier())->load($data['id'])
            ->getEntity()
            ->setDeleted(0)
            ->save();
        $this->baseTestAction(
            [
                'params' => ['action' => 'multiaction'],
                'get' => ['ids' => $data['id'] . ',' . $multiId],
            ]
        );
        $this->assertEquals(
            (new Application_Model_Entity_Entity_Carrier())->load($data['id'])
                ->getEntity()
                ->getDeleted(),
            '1'
        );
        $this->assertEquals(
            (new Application_Model_Entity_Entity_Carrier())->load($multiId)
                ->getEntity()
                ->getDeleted(),
            '1'
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
        if ($function) {
            $function();
        }
        $this->userPermissions(self::$user, $permissions);
        Application_Model_Entity_Accounts_User::login(self::$user->getId());
        $this->setStorage();
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
                'action' => 'info',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'carrier_view' => '0',
                ],
                'assert' => ['action' => 'edit'],
                'function' => false,
            ],
            [
                'action' => 'edit',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'carrier_view' => '0',
                ],
                'assert' => ['action' => 'edit'],
                'function' => false,
            ],
            [
                'action' => 'edit',
                'method' => 'post',
                'method_params' => [
                    'id' => '',
                    'entity_id' => '',
                    'tax_id' => '11-1' . random_int(100, 999) . random_int(100, 999),
                    'short_code' => random_int(1, 32000),
                    'name' => 'permissions' . random_int(1, 32000),
                    'contact' => '1',
                    'terms' => '1',
                    'contacts' => [
                        '1000' => [
                            'id' => '',
                            'entity_id' => '',
                            'contact_type' => '1',
                            'title' => 'Address',
                            'deleted' => '',
                            'value' => '{"address":"Pozharnyy lane","city":"","state":"","zip":""}',
                        ],
                        '1001' => [
                            'id' => '',
                            'entity_id' => '',
                            'contact_type' => '5',
                            'title' => 'Phone',
                            'deleted' => '',
                            'value' => '',
                        ],
                        '1002' => [
                            'id' => '',
                            'entity_id' => '',
                            'contact_type' => '9',
                            'title' => 'Fax',
                            'deleted' => '',
                            'value' => '',
                        ],
                        '1003' => [
                            'id' => '',
                            'entity_id' => '',
                            'contact_type' => '8',
                            'title' => 'Email',
                            'deleted' => '',
                            'value' => '',
                        ],
                    ],
                    'address' => 'Pozharnyy lane',
                    'city' => '',
                    'state' => '',
                    'zip' => '',
                    'correspondence_method' => '1',
                    'vendor' => [
                        '1000' => [
                            'id' => '',
                            'deleted' => '0',
                            'vendor_id' => '0',
                            'status' => '0',
                        ],
                    ],
                    'submit' => 'Save',
                ],
                'permissions' => [
                    'carrier_view' => '1',
                    'carrier_manage' => '0',
                ],
                'assert' => ['action' => 'edit'],
                'function' => false,
            ],
            [
                'action' => 'edit',
                'method' => 'get',
                'method_params' => ['id' => '1'],
                'permissions' => [
                    'carrier_view' => '1',
                ],
                'assert' => ['action' => 'edit'],
                'function' => false,
            ],
            [
                'action' => 'escrow',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'settlement_escrow_account_view' => '0',
                ],
                'assert' => ['action' => 'escrow'],
                'function' => false,
            ],
            [
                'action' => 'delete',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'carrier_manage' => '0',
                ],
                'assert' => ['action' => 'delete'],
                'function' => false,
            ],
            [
                'action' => 'multiaction',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'carrier_manage' => '0',
                ],
                'assert' => ['action' => 'multiaction'],
                'function' => false,
            ],
            [
                'action' => 'edit',
                'method' => 'get',
                'method_params' => ['id' => '1'],
                'permissions' => [
                    'carrier_view' => '1',
                ],
                'assert' => ['action' => 'edit'],
                'function' => function () {
                    $carrier = (new Application_Model_Entity_Entity_Carrier())->load('1');
                    $carrier->setData('status', '1')
                        ->save();
                },
            ],
            [                          //USER-CONTRACTOR
                'action' => 'edit',
                'method' => 'post',
                'method_params' => [
                    'id' => '1',
                    'entity_id' => '1',
                    'tax_id' => '11-1' . random_int(100, 999) . random_int(100, 999),
                    'short_code' => random_int(1, 32000),
                    'name' => 'permissions' . random_int(1, 32000),
                    'contact' => '1',
                    'terms' => '1',
                    'contacts' => [
                        '1000' => [
                            'id' => '',
                            'entity_id' => '',
                            'contact_type' => '1',
                            'title' => 'Address',
                            'deleted' => '',
                            'value' => '{"address":"Pozharnyy lane","city":"","state":"","zip":""}',
                        ],
                        '1001' => [
                            'id' => '',
                            'entity_id' => '',
                            'contact_type' => '5',
                            'title' => 'Phone',
                            'deleted' => '',
                            'value' => '',
                        ],
                        '1002' => [
                            'id' => '',
                            'entity_id' => '',
                            'contact_type' => '9',
                            'title' => 'Fax',
                            'deleted' => '',
                            'value' => '',
                        ],
                        '1003' => [
                            'id' => '',
                            'entity_id' => '',
                            'contact_type' => '8',
                            'title' => 'Email',
                            'deleted' => '',
                            'value' => '',
                        ],
                    ],
                    'address' => 'Pozharnyy lane',
                    'city' => '',
                    'state' => '',
                    'zip' => '',
                    'correspondence_method' => '1',
                    'vendor' => [
                        '1000' => [
                            'id' => '',
                            'deleted' => '0',
                            'vendor_id' => '0',
                            'status' => '0',
                        ],
                    ],
                    'submit' => 'Save',
                ],
                'permissions' => [
                    'carrier_manage' => '0',
                ],
                'assert' => ['action' => 'edit'],
                'function' => function () {
                    $carrier = $this->newCarrier();
                    $contractor = $this->newContractor($carrier);
                    self::$user = $this->newUser(
                        [
                            'entity_id' => $contractor->getData('entity_id'),
                            'role_id' => Application_Model_Entity_System_UserRoles::CONTRACTOR_ROLE_ID,
                        ]
                    );
                },
            ],
            [
                'action' => 'escrow',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'carrier_view' => '1',
                ],
                'assert' => ['action' => 'escrow'],
                'function' => false,
            ],
            [
                'action' => 'edit',
                'method' => 'post',
                'method_params' => [
                    'id' => '1',
                    'entity_id' => '1',
                    'tax_id' => '11-1' . random_int(100, 999) . random_int(100, 999),
                    'short_code' => random_int(1, 32000),
                    'name' => 'permissions' . random_int(1, 32000),
                    'contact' => '1',
                    'terms' => '1',
                    'redirect' => true,
                    'contacts' => [
                        '1000' => [
                            'id' => '',
                            'entity_id' => '',
                            'contact_type' => '1',
                            'title' => 'Address',
                            'deleted' => '',
                            'value' => '{"address":"Pozharnyy lane","city":"","state":"","zip":""}',
                        ],
                        '1001' => [
                            'id' => '',
                            'entity_id' => '',
                            'contact_type' => '5',
                            'title' => 'Phone',
                            'deleted' => '',
                            'value' => '',
                        ],
                        '1002' => [
                            'id' => '',
                            'entity_id' => '',
                            'contact_type' => '9',
                            'title' => 'Fax',
                            'deleted' => '',
                            'value' => '',
                        ],
                        '1003' => [
                            'id' => '',
                            'entity_id' => '',
                            'contact_type' => '8',
                            'title' => 'Email',
                            'deleted' => '',
                            'value' => '',
                        ],
                    ],
                    'address' => 'Pozharnyy lane',
                    'city' => '',
                    'state' => '',
                    'zip' => '',
                    'correspondence_method' => '1',
                    'vendor' => [
                        '1000' => [
                            'id' => '',
                            'deleted' => '0',
                            'vendor_id' => '0',
                            'status' => '0',
                        ],
                    ],
                    'submit' => 'Save',
                ],
                'permissions' => [
                    'carrier_manage' => '0',
                ],
                'assert' => ['action' => 'edit'],
                'function' => false,
            ],
        ];
    }
}
