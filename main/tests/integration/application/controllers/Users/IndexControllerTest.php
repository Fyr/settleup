<?php

class Users_IndexControllerTest extends BaseTestCase
{
    /** @var Users_IndexController */
    private $controller;

    protected function setUp(): void
    {
        $this->setDefaultController('users_index');
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

    public function testEditActionNewUser()
    {
        $post = [
            'id' => '',
            'entity_id' => '16',
            'name' => 'PHPUnit user POST' . random_int(1, 32000),
            'email' => 'phpunittest' . random_int(1, 32000) . '@pfleet.co',
            'password' => 'pass',
            'old_password' => '',
            'new_password' => '',
            'confirm_password' => '',
            'role_id' => Application_Model_Entity_System_UserRoles::CARRIER_ROLE_ID,
            'entity_id_title' => 'Phpunit',
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
            ],
            'address' => '',
            'city' => '',
            'state' => '',
            'zip' => '',
            'receive_notifications' => '0',
            'submit' => 'Save',
        ];

        $lastUser_id = (new Application_Model_Entity_Accounts_User())->getCollection()
            ->getLastItem()
            ->getId();
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $post,
            ]
        );
        $newUser_id = (new Application_Model_Entity_Accounts_User())->getCollection()
            ->getLastItem()
            ->getId();
        $this->assertEquals($lastUser_id + 1, $newUser_id);

        return $post;
    }

    /**
     * @depends testEditActionNewUser
     */
    public function testEditActionNewUserNotValid(array $data)
    {
        $befId = (new Application_Model_Entity_Accounts_User())->getCollection()
            ->getLastItem()
            ->getId();
        $data['name'] = '';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $this->assertEquals(
            (new Application_Model_Entity_Accounts_User())->getCollection()
                ->getLastItem()
                ->getId(),
            $befId
        );
    }

    /**
     * @depends testEditActionNewUser
     */
    public function testEditAction(array $data)
    {
        $data['id'] = (new Application_Model_Entity_Accounts_User())->load($data['name'], 'name')
            ->getId();
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => ['id' => $data['id']],
            ]
        );
        return $data;
    }

    /**
     * @depends testEditAction
     */
    public function testEditActionAdminRole(array $data)
    {
        $userCarrierId = (new Application_Model_Entity_Accounts_User())->getCollection()
            ->addFilter('role_id', '1', '<>')
            ->getFirstItem()
            ->getId();
        Application_Model_Entity_Accounts_User::login($userCarrierId);
        $data['role_id'] = Application_Model_Entity_System_UserRoles::SUPER_ADMIN_ROLE_ID;
        $data['entity_id'] = '';
        $data['redirect'] = 'index';
        $data['email'] = 'phpunittest' . random_int(1, 32000) . '@pfleet.co';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ],
            false
        );
        return $userCarrierId;
    }

    /**
     * @depends testEditAction
     */
    public function testEditActionContractorUser(array $data)
    {
        $carrier = $this->newCarrier();
        $contractor = $this->newContractor($carrier);
        $user = $this->newUser(
            [
                'entity_id' => $contractor->getData('entity_id'),
                'role_id' => Application_Model_Entity_System_UserRoles::CONTRACTOR_ROLE_ID,
            ]
        );

        Application_Model_Entity_Accounts_User::login($user->getId());
        $this->setStorage();

        $data['role_id'] = Application_Model_Entity_System_UserRoles::SUPER_ADMIN_ROLE_ID;
        $data['entity_id'] = '';
        $data['redirect'] = 'index';
        $data['email'] = 'phpunittest' . random_int(1, 32000) . '@pfleet.co';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ],
            false
        );
        return [
            'user-contractor_id' => $user->getId(),
            'edit_user_id' => (new Application_Model_Entity_Accounts_User())->getCollection()
                ->getLastItem()
                ->getId(),
        ];
    }

    /**
     * @depends testEditActionContractorUser
     */
    public function testEditActionContractorUserMethodGet(array $data)
    {
        Application_Model_Entity_Accounts_User::login($data['user-contractor_id']);
        $this->setStorage();

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => ['id' => $data['edit_user_id'] - 1],
            ],
            false
        );
    }

    public function testListNotAdmin()
    {
        $userCarrierId = (new Application_Model_Entity_Accounts_User())->getCollection()
            ->addFilter('role_id', '1', '=')
            ->getFirstItem()
            ->getId();

        Application_Model_Entity_Accounts_User::login($userCarrierId);
        $this->setStorage();
        $this->baseTestAction(
            [
                'params' => ['action' => 'list'],
                'get' => [],
            ],
            false
        );
    }

    /**
     * @depends testEditAction
     */
    public function testEditActionEditUser(array $data)
    {
        $data['name'] = $data['name'] . 'EDIT';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $this->assertEquals(
            (new Application_Model_Entity_Accounts_User())->load($data['id'])
                ->getName(),
            $data['name']
        );
        return $data;
    }

    /**
     * @depends testEditActionEditUser
     */
    public function testEditActionEditPasswordIncorrect(array $data)
    {
        $data['old_password'] = 'incorrect';
        $data['new_password'] = 'newPass';
        $data['confirm_password'] = 'newPass';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $this->assertNull(
            (new Application_Model_Entity_Accounts_User())->authUser($data['email'], $data['new_password'])
        );
        return $data;
    }

    /**
     * @depends testEditActionEditUser
     */
    public function testEditActionEditPasswordCorrectNoConfirm(array $data)
    {
        Application_Model_Entity_Accounts_User::login($this->_myUser);
        $storage = Zend_Auth::getInstance()
            ->getStorage()
            ->read();
        $storage->setPreviousUri('/uri');
        $data['old_password'] = $data['password'];
        $data['confirm_password'] = '';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ],
            false
        );
        $this->assertNull(
            (new Application_Model_Entity_Accounts_User())->authUser($data['email'], $data['new_password'])
        );
        return $data;
    }

    //    /**
    //     * @depends testEditActionEditPasswordIncorrect
    //     */
    //    public function testEditActionEditPasswordCorrect(array $data)
    //    {
    //        $data['old_password'] = $data['password'];
    //        $data['new_password'] = 'newPass';
    //        $data['confirm_password'] = 'newPass';
    //        $this->baseTestAction(
    //            array(
    //                'params' => array('action' => 'edit'),
    //                'post' => $data
    //            )
    //        );
    //        $this->assertNotNull(
    //            (new Application_Model_Entity_Accounts_User())->authUser($data['email'],$data['new_password'])
    //        );
    //    }

    /**
     * @depends testEditAction
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
            (new Application_Model_Entity_Accounts_User())->load($data['id'])
                ->getDeleted()
        );
    }

    /**
     * @depends testEditActionNewUser
     */
    public function testAddBeforeMultiAction(array $data)
    {
        $data['name'] = 'PHPUnit user MULTI' . random_int(1, 32000);
        $data['email'] = 'phpunittestMULTI' . random_int(1, 32000) . '@pfleet.co';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $multiId = (new Application_Model_Entity_Accounts_User())->load($data['name'], 'name')
            ->getId();
        return $multiId;
    }

    /**
     * @depends testEditAction
     * @depends testAddBeforeMultiAction
     */
    public function testMultiAction(array $data, $multiId)
    {
        (new Application_Model_Entity_Accounts_User())->load($data['id'])
            ->setDeleted(0)
            ->save();
        $this->baseTestAction(
            [
                'params' => ['action' => 'multiaction'],
                'ajax' => ['ids' => $data['id'] . ',' . $multiId],
            ]
        );
        $this->assertEquals(
            (new Application_Model_Entity_Accounts_User())->load($data['id'])
                ->getDeleted(),
            '1'
        );
        $this->assertEquals(
            (new Application_Model_Entity_Accounts_User())->load($multiId)
                ->getDeleted(),
            '1'
        );
    }

    //    /**
    //     * @depends testEditAction
    //     */
    //    public function testAddSelectedItemsAction(array $data)
    //    {
    //        (new Application_Model_Entity_Entity_Contractor())->getCollection()->getFirstItem()->getId();
    //        $this->baseTestAction(
    //            array(
    //                'params' => array('action' => 'addselecteditems'),
    //                'ajax'   => array('selectedItemsId'  => array($data['id'])),
    //            )
    //        );
    //    }

    public function testPermissionAction()
    {
        $carrier = $this->newCarrier();
        $user = $this->newUser(
            [
                'entity_id' => $carrier->getData('entity_id'),
                'role_id' => Application_Model_Entity_System_UserRoles::CARRIER_ROLE_ID,
            ]
        );

        $last_permission_id = (new Application_Model_Entity_Entity_Permissions())->getCollection()
            ->getLastItem()
            ->getId();

        $this->baseTestAction(
            [
                'params' => ['action' => 'permissions'],
                'get' => [
                    'id' => $user->getId(),
                ],
            ]
        );

        $this->assertEquals(
            (new Application_Model_Entity_Entity_Permissions())->getCollection()
                ->getLastItem()
                ->getId(),
            $last_permission_id + 1
        );

        return $user->getId();
    }

    /**
     * @depends testPermissionAction
     */
    public function testPermissionActionEditPermission($user_id)
    {
        $permissions_list = [
            'settlement_edit',
            'settlement_verify',
            'settlement_process',
            'settlement_delete',
            'settlement_approve',
            'settlement_reject',
            'settlement_data_view',
            'settlement_data_manage',
            'settlement_rule_view',
            'settlement_rule_manage',
            'settlement_escrow_account_view',
            'disbursement_view',
            'disbursement_manage',
            'disbursement_approve',
            'vendor_view',
            'vendor_manage',
            'reserve_account_vendor_view',
            'reserve_account_vendor_manage',
            'vendor_deduction_view',
            'vendor_deduction_manage',
            'carrier_view',
            'carrier_manage',
            'reserve_account_carrier_view',
            'reserve_account_carrier_manage',
            'contractor_view',
            'contractor_manage',
            'bank_account_contractor_view',
            'bank_account_contractor_manage',
            'reporting_ach_check',
            'reporting_deduction_remittance_file',
            'reporting_settlement_reconciliation',
            'reporting_general',
            'template_view',
            'template_manage',
            'uploading',
            'contractor_vendor_auth_manage',
            'permissions_manage',
        ];
        $data = array_fill_keys($permissions_list, '1');
        $data['settlement_edit'] = $data['settlement_verify'] = '0';
        $data['id'] = $user_id; //(new Application_Model_Entity_Entity_Permissions())->load($user_id,'user_id')->getId();
        $data['user_id'] = $user_id;
        $data['submit'] = 'Save';

        $this->baseTestAction(
            [
                'params' => ['action' => 'permissions'],
                'post' => $data,
            ]
        );

        $current_permission = (new Application_Model_Entity_Entity_Permissions())->load($data['id']);

        $this->assertEquals(
            $current_permission->getData('settlement_edit') + $current_permission->getData('settlement_verify'),
            '0'
        );
    }

    //  PERMISSIONS

    public function testHasNoPermissionListAction()
    {
        $carrier = $this->newCarrier();
        $user = $this->newUser(
            [
                'entity_id' => $carrier->getData('entity_id'),
                'role_id' => Application_Model_Entity_System_UserRoles::CARRIER_ROLE_ID,
            ]
        );
        $this->userPermissions($user, ['permissions_manage' => '0']);

        Application_Model_Entity_Accounts_User::login($user->getId());
        $this->setStorage();

        $this->baseTestAction(
            [
                'params' => ['action' => 'list'],
                'get' => [],
            ],
            false
        );
        return $user->getId();
    }

    /**
     * @depends testHasNoPermissionListAction
     * @param $user_id int
     */
    public function testPermissionActionUserCarrier($user_id)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'permissions'],
                'get' => ['id' => $user_id],
            ]
        );
    }

    /**
     * @depends testHasNoPermissionListAction
     */
    public function testHasNoPermissionUserCarrierEditAnotherCarrier($user_id)
    {
        Application_Model_Entity_Accounts_User::login($user_id);
        $this->setStorage();

        $this->baseTestAction(
            [
                'params' => ['action' => 'permissions'],
                'get' => ['id' => '1'],
            ],
            false
        );
    }

    public function testPermissionActionUserVendor()
    {
        $carrier = $this->newCarrier();
        $vendor = $this->newVendor($carrier);

        $user = $this->newUser(
            [
                'entity_id' => $vendor->getData('entity_id'),
                'role_id' => Application_Model_Entity_System_UserRoles::VENDOR_ROLE_ID,
            ]
        );

        $this->baseTestAction(
            [
                'params' => ['action' => 'permissions'],
                'get' => ['id' => $user->getId()],
            ]
        );
    }
}
