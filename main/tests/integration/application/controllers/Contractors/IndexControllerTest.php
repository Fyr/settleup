<?php

class Contractors_IndexControllerTest extends BaseTestCase
{
    /** @var Contractors_IndexController */
    private $controller;

    protected function setUp(): void
    {
        $this->setDefaultController('contractors_index');
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

    public function testListActionVendorUser()
    {
        $userVendorId = $this->getUserByRole(Application_Model_Entity_System_UserRoles::VENDOR_ROLE_ID)
            ->getId();
        Application_Model_Entity_Accounts_User::login($userVendorId);
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

    public function testEditActionNewContractor()
    {
        $post = [
            'id' => '',
            'carrier_id' => '',
            'status' => '',
            'code' => random_int(1, 32000),
            'entity_id' => '',
            'company_name' => 'PHPUnit contractor POST' . random_int(1, 32000),
            'first_name' => 'FirstName',
            'last_name' => 'LastName',
            'tax_id' => '32-1' . random_int(100, 999) . random_int(100, 999),
            'social_security_id' => '323-23-' . random_int(1000, 9999),
            'dob' => '2014-01-01',
            'driver_license' => random_int(1, 32000),
            'state_of_operation' => '-',
            'expires' => '',
            'classification' => 'Classification',
            'division' => 'Division',
            'department' => 'Department',
            'route' => 'Route',
            'contacts' => [
                1006 => [
                    'id' => '',
                    'entity_id' => '',
                    'contact_type' => '1',
                    'title' => 'Address',
                    'deleted' => '',
                    'value' => '{"address":"Pozharnyy lane","city":"","state":"","zip":""}',
                ],
                1001 => [
                    'id' => '',
                    'entity_id' => '',
                    'contact_type' => '5',
                    'title' => 'Phone',
                    'deleted' => '',
                    'value' => '',
                ],
                1002 => [
                    'id' => '',
                    'entity_id' => '',
                    'contact_type' => '9',
                    'title' => 'Fax',
                    'deleted' => '',
                    'value' => '',
                ],
                1003 => [
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
            'correspondence_method' => '10',
            'carrier_status_id' => '0',
            'vendor' => [
                1000 => [
                    'id' => '',
                    'deleted' => '0',
                    'vendor_id' => '0',
                    'status' => '0',
                ],
            ],
            'submit' => 'Save',
        ];

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $post,
            ]
        );
        return $post;
    }

    /**
     * @depends testEditActionNewContractor
     */
    public function testEditActionEditContractor(array $data)
    {
        $contractor = (new Application_Model_Entity_Entity_Contractor())->load($data['company_name'], 'company_name');
        $this->newBankAccount($contractor);//bank account
        $contractor->setStatus('1')
            ->save();
        $data['id'] = $contractor->getId();
        $data['entity_id'] = $contractor->getEntity()
            ->getId();
        $data['status'] = $contractor->getStatus();
        $data['company_name'] = $data['company_name'] . 'EDIT';

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $this->assertEquals(
            (new Application_Model_Entity_Entity_Contractor())->load($data['company_name'], 'company_name')
                ->getId(),
            $data['id']
        );
        return $data;
    }

    //    /**
    //     * @depends testEditActionEditContractor
    //     */
    //    public function testEditActionNoPermissionsEditUserCarrier($data)
    //    {
    //        $user = $this->newUser(array(
    //                'role_id' => Application_Model_Entity_System_UserRoles::CARRIER_ROLE_ID,
    //                'entity_id' => (new Application_Model_Entity_Entity())->getCollection()
    //                    ->addFilter('entity_type_id',Application_Model_Entity_System_UserRoles::CARRIER_ROLE_ID)
    //                    ->getFirstItem()->getId()
    //            ));
    //        $this->userPermissions($user, array(
    //                Application_Model_Entity_Entity_Permissions::CONTRACTOR_VIEW   => '0',
    //                Application_Model_Entity_Entity_Permissions::CONTRACTOR_MANAGE => '0'
    //            ));
    //        Application_Model_Entity_Accounts_User::login($user->getId());
    //        $this->setStorage();
    //
    //        $this->baseTestAction(
    //            array(
    //                'params' => array('action' => 'edit'),
    //                'post'   => $data,
    //            ),
    //            false
    //        );
    //    }

    /**
     * @depends testEditActionEditContractor
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
     * @depends testEditActionEditContractor
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
            (new Application_Model_Entity_Entity_Contractor())->load($data['id'])
                ->getTaxId(),
            $data['tax_id']
        );
    }

    /**
     * @depends testEditActionEditContractor
     */
    public function testActiveAction(array $data)
    {
        (new Application_Model_Entity_Entity_Contractor())->load($data['id'])
            ->setStatus('1')
            ->save();
        $this->baseTestAction(
            [
                'params' => ['action' => 'changestatus'],
                'get' => [
                    'id' => $data['id'],
                    'status' => 'STATUS_ACTIVE',
                ],
            ]
        );
        $this->assertEquals(
            (new Application_Model_Entity_Entity_Contractor())->load($data['id'])
                ->getStatus(),
            Application_Model_Entity_System_ContractorStatus::STATUS_ACTIVE
        );
    }

    /**
     * @depends testEditActionEditContractor
     */
    public function testTerminateAction(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'changestatus'],
                'get' => [
                    'id' => $data['id'],
                    'status' => 'STATUS_TERMINATED',
                ],
            ]
        );
        $this->assertEquals(
            (new Application_Model_Entity_Entity_Contractor())->load($data['id'])
                ->getStatus(),
            Application_Model_Entity_System_ContractorStatus::STATUS_TERMINATED
        );
    }

    /**
     * @depends testEditActionEditContractor
     */
    public function testRehireAction(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'changestatus'],
                'get' => [
                    'id' => $data['id'],
                    'status' => 'STATUS_ACTIVE',
                ],
            ]
        );
        $this->assertEquals(
            (new Application_Model_Entity_Entity_Contractor())->load($data['id'])
                ->getStatus(),
            Application_Model_Entity_System_ContractorStatus::STATUS_ACTIVE
        );
    }

    public function testDeleteActionContactInfo()
    {
        $rndContactInfoId = (new Application_Model_Entity_Entity_Contact_Info())->getCollection()
            ->getFirstItem()
            ->getId();
        $this->baseTestAction(
            [
                'params' => ['action' => 'delete'],
                'post' => [
                    'contact' => $rndContactInfoId,
                    'id' => '1',
                ],
            ]
        );
        $this->assertEquals(
            '0',
            is_countable((new Application_Model_Entity_Entity_Contact_Info())->getCollection()
                ->addFilter('id', $rndContactInfoId)) ? count(
                (new Application_Model_Entity_Entity_Contact_Info())->getCollection()
                    ->addFilter('id', $rndContactInfoId)
            ) : 0
        );
    }

    /**
     * @depends testEditActionEditContractor
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
            '1',
            (new Application_Model_Entity_Entity_Contractor())->load($data['id'])
                ->getEntity()
                ->getDeleted()
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
        // @todo fix password
        $this->loginUser(self::$user->getId(), $this->defaultPassMd5);
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
                    'contractor_view' => '0',
                ],
                'assert' => ['action' => 'edit'],
                'function' => false,
            ],
            [
                'action' => 'edit',
                'method' => 'post',
                'method_params' => [
                    'id' => '',
                    'carrier_id' => '',
                    'status' => '',
                    'code' => random_int(1, 32000),
                    'entity_id' => '',
                    'company_name' => 'PHPUnit contractor POST' . random_int(1, 32000),
                    'first_name' => 'FirstName',
                    'last_name' => 'LastName',
                    'tax_id' => '32-1' . random_int(100, 999) . random_int(100, 999),
                    'social_security_id' => '323-23-' . random_int(1000, 9999),
                    'dob' => '2014-01-01',
                    'driver_license' => random_int(1, 32000),
                    'state_of_operation' => '-',
                    'expires' => '',
                    'classification' => 'Classification',
                    'division' => 'Division',
                    'department' => 'Department',
                    'route' => 'Route',
                    'contacts' => [
                        1006 => [
                            'id' => '',
                            'entity_id' => '',
                            'contact_type' => '1',
                            'title' => 'Address',
                            'deleted' => '',
                            'value' => '{"address":"Pozharnyy lane","city":"","state":"","zip":""}',
                        ],
                        1001 => [
                            'id' => '',
                            'entity_id' => '',
                            'contact_type' => '5',
                            'title' => 'Phone',
                            'deleted' => '',
                            'value' => '',
                        ],
                        1002 => [
                            'id' => '',
                            'entity_id' => '',
                            'contact_type' => '9',
                            'title' => 'Fax',
                            'deleted' => '',
                            'value' => '',
                        ],
                        1003 => [
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
                    'correspondence_method' => '10',
                    'carrier_status_id' => '0',
                    'vendor' => [
                        1000 => [
                            'id' => '',
                            'deleted' => '0',
                            'vendor_id' => '0',
                            'status' => '0',
                        ],
                    ],
                    'submit' => 'Save',
                ],
                'permissions' => [
                    'contractor_view' => '1',
                    'contractor_manage' => '0',
                ],
                'assert' => ['action' => 'edit'],
                'function' => false,
            ],
            [
                'action' => 'edit',
                'method' => 'get',
                'method_params' => ['id' => '1'],
                'permissions' => [
                    'contractor_view' => '1',
                ],
                'assert' => ['action' => 'edit'],
                'function' => false,
            ],
            [
                'action' => 'delete',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'contractor_manage' => '0',
                ],
                'assert' => ['action' => 'delete'],
                'function' => false,
            ],
            [
                'action' => 'changestatus',//Application_Model_Entity_System_ContractorStatus::
                'method' => 'get',
                'method_params' => ['id' => '2', 'status' => 'STATUS_ACTIVE'],
                'permissions' => [
                    'contractor_manage' => '0',
                ],
                'assert' => ['action' => 'changestatus'],
                'function' => false,
            ],
            [
                'action' => 'list',
                'method' => 'get',
                'method_params' => [],
                'permissions' => [
                    'contractor_view' => '0',
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
            //            array(                          //USER-CONTRACTOR
            //                'action' => 'edit',
            //                'method' => 'get',
            //                'method_params' => array(),
            //                'permissions' => array(
            //                    'template_manage' => '0',
            //                ),
            //                'assert' => array('action' => 'edit'),
            //                'function' => function(){
            //                    $carrier = $this->newCarrier();
            //                    $contractor = $this->newContractor($carrier);
            //                    self::$user = $this->newUser(array(
            //                        'entity_id' => $contractor->getData('entity_id'),
            //                        'role_id'   => Application_Model_Entity_System_UserRoles::CONTRACTOR_ROLE_ID,
            //                        )
            //                    );
            //                },
            //            ),
            //            array(
            //                'action' => 'list',
            //                'method' => 'get',
            //                'method_params' => array(),
            //                'permissions' => array(
            //                    'contractor_view'=> '0',
            //                ),
            //                'assert' => array('action' => 'list'),
            //                'function' => false,
            //            ),
            //            array(                      //USER-VENDOR
            //                'action' => 'edit',
            //                'method' => 'get',
            //                'method_params' => array(),
            //                'permissions' => array(
            //                    'template_manage' => '0',
            //                ),
            //                'assert' => array('action' => 'edit'),
            //                'function' => function(){
            //                    $carrier = $this->newCarrier();
            //                    $vendor = $this->newVendor($carrier);
            //                    self::$user = $this->newUser(array(
            //                            'entity_id' => $vendor->getData('entity_id'),
            //                            'role_id'   => Application_Model_Entity_System_UserRoles::VENDOR_ROLE_ID,
            //                        )
            //                    );
            //                },
            //            ),
            //            array(
            //                'action' => 'edit',
            //                'method' => 'post',
            //                'method_params' => array(
            //                    'id' =>'',
            //                    'carrier_id' => '',
            //                    'status' => '',
            //                    'code' => rand(1,32000),
            //                    'entity_id' => '',
            //                    'company_name' => 'PHPUnit contractor POST'. rand(1,32000),
            //                    'first_name' =>  'FirstName',
            //                    'last_name' => 'LastName',
            //                    'tax_id' =>  '32-1' . rand(100,999) . rand(100,999),
            //                    'social_security_id' => '323-23-' . rand(1000,9999),
            //                    'dob' => '2014-01-01',
            //                    'driver_license' => rand(1,32000),
            //                    'state_of_operation' => '-',
            //                    'expires' => '',
            //                    'classification' => 'Classification',
            //                    'division' => 'Division',
            //                    'department' => 'Department',
            //                    'route' => 'Route',
            //                    'contacts' =>
            //                        array (
            //                            1006 =>
            //                                array (
            //                                    'id' =>'',
            //                                    'entity_id' => '',
            //                                    'contact_type' => '1',
            //                                    'title' => 'Address',
            //                                    'deleted' => '',
            //                                    'value' => '{"address":"Pozharnyy lane","city":"","state":"","zip":""}'
            //                                ),
            //                            1001 =>
            //                                array (
            //                                    'id' => '',
            //                                    'entity_id' => '',
            //                                    'contact_type' => '5',
            //                                    'title' => 'Phone',
            //                                    'deleted' => '',
            //                                    'value' => ''
            //                                ),
            //                            1002 =>
            //                                array (
            //                                    'id' => '',
            //                                    'entity_id' =>'',
            //                                    'contact_type' =>'9',
            //                                    'title' => 'Fax',
            //                                    'deleted' => '',
            //                                    'value' => ''
            //                                ),
            //                            1003 =>
            //                                array (
            //                                    'id' => '',
            //                                    'entity_id' => '',
            //                                    'contact_type' => '8',
            //                                    'title' => 'Email',
            //                                    'deleted' => '',
            //                                    'value' => ''
            //                                )
            //                        ),
            //                    'address' => 'Pozharnyy lane',
            //                    'city' => '',
            //                    'state' => '',
            //                    'zip' => '',
            //                    'correspondence_method' => '10',
            //                    'carrier_status_id' => '0',
            //                    'vendor' =>
            //                        array (
            //                            1000 =>
            //                                array (
            //                                    'id' => '',
            //                                    'deleted' => '0',
            //                                    'vendor_id' => '0',
            //                                    'status' => '0'
            //                                )
            //                        ),
            //                    'submit' => 'Save',
            //                ),
            //                'permissions' => array(
            //                    'contractor_view' => '1',
            //                    'contractor_manage' => '0'
            //                ),
            //                'assert' => array('action' => 'edit'),
            //                'function' => false,
            //            ),
        ];
    }
}
