<?php

class Bankaccounts_IndexControllerTest extends BaseTestCase
{
    /** @var Bankaccounts_IndexController */
    private $controller;

    protected function setUp(): void
    {
        $this->setDefaultController('bankaccounts_index');
        parent::setUp();
    }

    public function testListAction()
    {
        $this->userPermissions($this->_myUser);
        $this->baseTestAction(
            [
                'params' => ['action' => 'list'],
                'get' => [
                    'entity' => (new Application_Model_Entity_Entity_Contractor())->getCollection()
                        ->getFirstItem()
                        ->getEntityId(),
                ],
            ]
        );
    }

    public function testIndexAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'index'],
                'get' => ['entity' => '1'],
                'assert' => ['action' => 'list'],
            ]
        );
    }

    public function testNewAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'new'],
                'get' => ['entity' => '1'],
                'assert' => ['action' => 'edit'],
            ]
        );
    }

    public function testEditActionNewBankAccountCheck()
    {
        $post = [
            'id' => '',
            'entity' => '1',
            'entity_id' => '',
            'entity_id_title' => 'PHPunitTest',
            'account_nickname' => 'PHPUnit bank account ' . random_int(1, 32000),
            'process_type' => '1',
            'payment_type' => '1',
            'limit_type' => '2',
            'amount' => '20000',
            'percentage' => '',
            'account_type' => '1',
            'name_on_account' => 'PHP Unit Test',
            'ACH_bank_routing_id' => '987',
            'ACH_bank_account_id' => '456',
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
     * @depends testEditActionEditBankAccountCheck
     */
    public function testEditAction(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => [
                    'id' => $data['id'],
                    'entity' => '1',
                ],
            ]
        );
    }

    public function testEditActionPaymentTypeInvalid()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => [
                    'entity' => '1',
                    'payment_type' => '4',
                ],
                'assert' => [
                    'controller' => 'error',
                    'action' => 'error',
                ],
            ]
        );
    }

    public function testEditActionPaymentType()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => [
                    'entity' => '1',
                    'payment_type' => '1',
                ],
            ]
        );
    }

    public function testEditActionNewBankAccountAch()
    {
        $post = [
            'id' => '',
            'entity' => (new Application_Model_Entity_Entity_Vendor())->getCollection()
                ->getFirstItem()
                ->getEntityId(),
            'bank_account_id' => '',
            'priority' => '',
            'entity_id_title' => 'PHPunitTest',
            'account_nickname' => 'PHPUnit bank account Ach' . random_int(1, 32000),
            'process_type' => '1',
            'payment_type' => '2',
            'limit_type' => '1',
            'amount' => '',
            'percentage' => '20',
            'account_type' => '1',
            'name_on_account' => 'PHP Unit Test',
            'ACH_bank_routing_id' => '123456789',
            'ACH_bank_account_id' => '123456789',
            'card_number' => '123456789',
            'name_on_card' => '',
            'CC_billing_address' => '',
            'CC_city' => '',
            'CC_state' => '',
            'CC_zip' => '',
            'expiration_date' => '',
            'cvs_code' => '',
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
     * @depends testEditActionNewBankAccountAch
     */
    public function testDeleteActionAch(array $data)
    {
        $account = (new Application_Model_Entity_Accounts_Bank())->load($data['account_nickname'], 'account_nickname');
        $this->assertEquals(
            '1',
            (new Application_Model_Entity_Accounts_Bank())->load($account->getId())
                ->getDeleted()
        );
        return $account->getId();
    }

    public function testEditActionNewBankAccountCC()
    {
        $post = [
            'id' => '',
            'entity' => '2',
            'entity_id' => (new Application_Model_Entity_Entity_Contractor())->getCollection()
                ->getFirstItem()
                ->getEntityId(),
            'bank_account_id' => '',
            'priority' => '',
            'entity_id_title' => 'PHPunitTest',
            'account_nickname' => 'PHPUnit bank account CС' . random_int(1, 32000),
            'process_type' => '1',
            'process_type_title' => 'Settlement',
            'payment_type' => '3',
            'limit_type' => '1',
            'amount' => '',
            'percentage' => '20',
            'card_number' => '12345678',
            'account_type' => '1',
            'name_on_account' => 'PHP Unit Test',
            'ACH_bank_routing_id' => '123456789',
            'ACH_bank_account_id' => '543211239',
            'redirect' => 'index',
            'submit' => 'Save',
            /*
            'name_on_card' =>  '123',
            'CC_billing_address' =>  '123',
            'CC_city' =>  'City',
            'CC_state' =>  'State',
            'CC_zip' =>  '123',
            'expiration_date' => '01-01-2040',
            'cvs_code' =>  '123',*/
        ];
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $post,
            ]
        );
        return $post;
    }

    public function testEditActionGetEntityTypes()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => [
                    'entity' => (new Application_Model_Entity_Entity_Contractor())->getCollection()
                        ->getFirstItem()
                        ->getEntityId(),
                ],
            ]
        );

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => [
                    'entity' => (new Application_Model_Entity_Entity_Vendor())->getCollection()
                        ->getFirstItem()
                        ->getEntityId(),
                ],
            ]
        );
    }

    public function testInfoActionWithoutParams()
    {
        $userCarrier = $this->getUserByRole(Application_Model_Entity_System_UserRoles::CARRIER_ROLE_ID);
        $userCarrier->setLastSelectedCarrier()
            ->setEntityId()
            ->save();
        Application_Model_Entity_Accounts_User::login($userCarrier->getId());
        $this->baseTestAction(
            [
                'params' => ['action' => 'info'],
            ],
            false
        );
    }

    public function testInfoActionByCarrier()
    {
        $carrier = $this->newCarrier();
        $this->baseTestAction(
            [
                'params' => ['action' => 'info'],
                'get' => ['entity' => $carrier->getEntityId()],
            ]
        );
        return $carrier;
    }
}
