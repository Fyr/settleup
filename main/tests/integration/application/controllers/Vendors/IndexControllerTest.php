<?php

class Vendors_IndexControllerTest extends BaseTestCase
{
    /** @var Vendors_IndexController */
    private $controller;
    public static $vendor_id;

    protected function setUp(): void
    {
        $this->setDefaultController('vendors_index');
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

    public function testEditActionNewVendor()
    {
        $post = [
            'id' => '',
            'entity_id' => '',
            'code' => 'code' . random_int(1, 32000),
            'name' => 'PHPunit vendor POST' . random_int(1, 32000),
            'contact' => 'contact',
            'tax_id' => '33-3' . random_int(100, 999) . random_int(100, 999),
            'contacts' => [
                '1000' => [
                    'id' => '',
                    'entity_id' => '',
                    'contact_type' => '1',
                    'title' => 'Address',
                    'deleted' => '',
                    'value' => '{"address":"","city":"","state":"","zip":""}',
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
            'terms' => '1',
            'submit' => 'Save',
        ];

        $last_vendor_id = (new Application_Model_Entity_Entity_Vendor())->getCollection()
            ->getLastItem()
            ->getId();

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $post,
            ]
        );

        self::$vendor_id = (new Application_Model_Entity_Entity_Vendor())->getCollection()
            ->getLastItem()
            ->getId();
        $this->assertEquals(
            self::$vendor_id,
            $last_vendor_id + 1
        );

        return $post;
    }

    /**
     * @depends testEditActionNewVendor
     */
    public function testEditActionEditVendor(array $data)
    {
        $vendor = (new Application_Model_Entity_Entity_Vendor())->load(self::$vendor_id);
        $this->newBankAccount($vendor);
        $vendor->setData('status', Application_Model_Entity_System_SystemValues::CONFIGURED_STATUS)
            ->save();
        $vendor = (new Application_Model_Entity_Entity_Vendor())->load(self::$vendor_id);
        $data['id'] = $vendor->getId();
        $data['entity_id'] = $vendor->getEntity()
            ->getId();
        $data['name'] = $data['name'] . 'EDIT';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $this->assertEquals(
            $data['id'],
            (new Application_Model_Entity_Entity_Vendor())->load($data['name'], 'name')
                ->getId()
        );
        return $data;
    }

    public function testEditAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => ['id' => self::$vendor_id],
            ]
        );
    }

    /**
     * @depends testEditActionEditVendor
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
            (new Application_Model_Entity_Entity_Vendor())->load($data['id'])
                ->getTaxId(),
            $data['tax_id']
        );
    }

    public function testDeleteAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'delete'],
                'get' => ['id' => self::$vendor_id],
            ]
        );
        $this->assertEquals(
            '1',
            (new Application_Model_Entity_Entity_Vendor())->load(self::$vendor_id)
                ->getEntity()
                ->getDeleted()
        );
    }

    public function testEditActionAfterDelete()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => ['id' => self::$vendor_id],
            ]
        );
    }

    //    /**
    //     * @depends testEditActionNewVendor
    //     */
    //    public function testAddBeforeMultiAction(array $data)
    //    {
    //        $data['name'] = 'PHPUnit vendor MULTI' . rand(1,32000);
    //        $data['code'] = rand(1,32000);
    //        $this->baseTestAction(
    //            array(
    //                'params' => array('action' => 'edit'),
    //                'post'   => $data,
    //            )
    //        );
    //        $multiId = (new Application_Model_Entity_Entity_Vendor())->load($data['name'], 'name')->getId();
    //        return $multiId;
    //    }
    //
    //    /**
    //     * @depends testEditActionEditVendor
    //     * @depends testAddBeforeMultiAction
    //     */
    //    public function testMultiAction(array $data, $multiId)
    //    {
    //        (new Application_Model_Entity_Entity_Vendor())->load($data['id'])->setDeleted(0)->save();
    //        $this->baseTestAction(
    //            array(
    //                'params' => array('action' => 'multiaction'),
    //                'ajax'   => array('ids'    => $data['id'] . ','. $multiId),
    //            )
    //        );
    //        $this->assertEquals(
    //            (new Application_Model_Entity_Entity_Vendor())->load($data['id'])->getEntity()->getDeleted(),'1');
    //        $this->assertEquals(
    //            (new Application_Model_Entity_Entity_Vendor())->load($multiId)->getEntity()->getDeleted(),'1');
    //    }

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
                'action' => 'edit',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'vendor_view' => '0',
                ],
                'assert' => ['action' => 'edit'],
                'function' => false,
            ],
            [
                'action' => 'list',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'vendor_view' => '0',
                ],
                'assert' => ['action' => 'list'],
                'function' => false,
            ],
            [
                'action' => 'delete',
                'method' => 'get',
                'method_params' => ['id' => '1'],
                'permissions' => [
                    'vendor_manage' => '0',
                ],
                'assert' => ['action' => 'delete'],
                'function' => false,
            ],
            [
                'action' => 'edit',
                'method' => 'post',
                'method_params' => [
                    'id' => '',
                    'entity_id' => '',
                    'code' => 'code' . random_int(1, 32000),
                    'name' => 'PHPunit vendor POST' . random_int(1, 32000),
                    'contact' => 'contact',
                    'tax_id' => '33-3' . random_int(100, 999) . random_int(100, 999),
                    'contacts' => [
                        '1000' => [
                            'id' => '',
                            'entity_id' => '',
                            'contact_type' => '1',
                            'title' => 'Address',
                            'deleted' => '',
                            'value' => '{"address":"","city":"","state":"","zip":""}',
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
                    'terms' => '1',
                    'submit' => 'Save',
                ],
                'permissions' => [
                    'vendor_view' => '1',
                    'vendor_manage' => '0',
                ],
                'assert' => ['action' => 'edit'],
                'function' => function () {
                    $vendor = (new Application_Model_Entity_Entity_Vendor())->load(self::$vendor_id);
                    $vendor->setData('status', '1')
                        ->save();
                },
            ],
        ];
    }
}
