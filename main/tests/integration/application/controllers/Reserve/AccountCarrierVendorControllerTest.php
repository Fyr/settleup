<?php

class Reserve_AccountCarrierVendorControllerTest extends BaseTestCase
{
    /** @var Reserve_AccountcarriervendorController */
    private $controller;

    protected function setUp(): void
    {
        $this->setDefaultController('reserve_accountcarriervendor');
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
                'assert' => ['action' => 'index'],
            ]
        );
    }

    public function testNewAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'new'],
                'assert' => ['action' => 'new'],
            ]
        );
    }

    public function testEditActionNewAccount()
    {
        $reserveIdBefore = (new Application_Model_Entity_Accounts_Reserve())->getCollection()
            ->getLastItem()
            ->getId();
        $carrier = $this->newCarrier();
        (new Application_Model_Entity_Accounts_User())->load(16)
            ->setData('last_selected_carrier', $carrier->getId())
            ->save();
        $post = [
            'id' => '',
            'entity_id' => $carrier->getEntityId(),
            'bank_account_id' => '1',
            'bank_account_id_title' => 'Priorbank Name',
            'account_name' => 'Unit Test',
            'description' => 'PHPUnit CarrierVendor RA POST' . random_int(1, 32000),
            'min_balance' => '',
            'contribution_amount' => '',
            'initial_balance' => '',
            'current_balance' => '',
            'disbursement_code' => '',
            'entity_id_title' => 'Soco Fuel Cards',
            'vendor_reserve_code' => '',
            'reserve_account_id' => '',
            'submit' => 'Submit',
        ];
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $post,
            ]
        );
        $reserveIdAfter = (new Application_Model_Entity_Accounts_Reserve())->getCollection()
            ->getLastItem()
            ->getId();
        $this->assertNotEquals($reserveIdBefore, $reserveIdAfter);
        return $post;
    }

    public function testEditActionGet()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => [
                    'id' => '-1',
                    'entity' => '1',
                ],
            ]
        );
    }

    public function testEditActionGetNoEntity()
    {
        $userVendorId = (new Application_Model_Entity_Accounts_User())->getCollection()
            ->addFilter('role_id', '4')
            ->getFirstItem()
            ->getId();
        Application_Model_Entity_Accounts_User::login($userVendorId);
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => [
                    'id' => '1',
                    'entity' => '-1',
                ],
            ],
            false
        );
    }

    /**
     * @depends testEditActionNewAccount
     */
    public function testEditActionNotValid(array $data)
    {
        $RAR = new Application_Model_Entity_Accounts_Reserve();
        $RARIdBefore = $RAR->getCollection()
            ->getLastItem()
            ->getId();
        $data['description'] = '';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $RARIdAfter = $RAR->getCollection()
            ->getLastItem()
            ->getId();
        $this->assertEquals($RARIdBefore, $RARIdAfter);
    }

    /**
     * @depends testEditActionNewAccount
     */
    public function testEditActionShow(array $data)
    {
        $RAR = new Application_Model_Entity_Accounts_Reserve();
        $RAR->load($data['description'], 'description');

        $model = new Application_Model_Entity_Accounts_Reserve_Vendor();
        $model->load($RAR->getId(), 'reserve_account_id');

        $data['id'] = $model->getId();

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => ['id' => $data['id']],
            ]
        );
        return $data;
    }

    /**
     * @depends testEditActionShow
     */
    public function testDeleteAction(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'delete'],
                'get' => ['id' => $data['id']],
            ]
        );
        $RAV = new Application_Model_Entity_Accounts_Reserve_Vendor();
        $RAVDeleted = $RAV->load($data['id'])
            ->getDeleted();
        $this->assertEquals($RAVDeleted, '1');
    }

    /**
     * @depends testEditActionShow
     */
    public function testAddBeforeMultiAction(array $data)
    {
        $multi['entity_id'] = $data['entity_id'];
        $multi['idv1'] = $data['id'];
        $data['id'] = '';
        $data['description'] = 'PHPUnit CarrierVendor RA2 POST' . random_int(1, 32000);
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $RAR = new Application_Model_Entity_Accounts_Reserve();
        $RAR->load($data['description'], 'description');
        $multi['idv2'] = $RAR->getVendorAccount()
            ->getId();
        return $multi;
    }

    /**
     * @depends testAddBeforeMultiAction
     */
    public function testListFilterAction(array $data)
    {
        Application_Model_Entity_Accounts_User::login(16);
        $this->setStorage();
        $this->baseTestAction(
            [
                'params' => ['action' => 'list'],
                'get' => ['entity' => $data['entity_id']],
            ],
            false
        );
    }

    /**
     * @depends testAddBeforeMultiAction
     */
    public function testMultiAction(array $data)
    {
        Application_Model_Entity_Accounts_User::login(16);
        $RAV = new Application_Model_Entity_Accounts_Reserve_Vendor();
        $data['id1'] = $RAV->load($data['idv1'])
            ->getReserveAccountEntity()
            ->setDeleted('0')
            ->save()
            ->getId();
        $data['id2'] = $RAV->load($data['idv2'])
            ->getReserveAccountEntity()
            ->setDeleted('0')
            ->save()
            ->getId();
        $this->baseTestAction(
            [
                'params' => ['action' => 'multiaction'],
                'ajax' => ['ids' => $data['idv1'] . ',' . $data['idv2']],
            ]
        );
        $RAR = new Application_Model_Entity_Accounts_Reserve();
        $this->assertEquals(
            $RAR->load($data['id1'])
                ->getDeleted(),
            '1'
        );
        $this->assertEquals(
            $RAR->load($data['id2'])
                ->getDeleted(),
            '1'
        );
    }
}
