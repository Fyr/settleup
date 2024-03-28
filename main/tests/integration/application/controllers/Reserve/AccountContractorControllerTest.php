<?php

class Reserve_AccountContractorControllerTest extends BaseTestCase
{
    /** @var Reserve_AccountcontractorController */
    private $controller;
    public static $rAE;

    protected function setUp(): void
    {
        $this->setDefaultController('reserve_accountcontractor');
        parent::setUp();
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

    public function testViewAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'view'],
                'assert' => ['action' => 'edit'],
            ]
        );
    }

    public function testListAction()
    {
        self::$rAE = new Application_Model_Entity_Accounts_Reserve();
        $this->baseTestAction(
            [
                'params' => ['action' => 'list'],
            ]
        );
    }

    public function testEditActionNewAccount()
    {
        $reserveIdBefore = (new Application_Model_Entity_Accounts_Reserve())->getCollection()
            ->getLastItem()
            ->getId();
        $carrier = $this->newCarrier();
        $raC = $this->newReserveAccount($carrier);
        $contractor = $this->newContractor($carrier);
        (new Application_Model_Entity_Accounts_User())->load(16)
            ->setData('last_selected_carrier', $carrier->getId())
            ->setData('last_selected_contractor', $contractor->getId())
            ->save();

        $post = [
            'id' => '',
            'entity_id' => $contractor->getEntityId(),
            'reserve_account_id' => '',
            'balance' => '',
            'reserve_account_vendor_id' => $raC->getId(),
            'vendor_id' => $carrier->getEntityId(),
            'priority' => '1',
            'account_name' => 'PHPUnitTest 2109 RAC',
            'description' => 'PhpUnitTest RAC1 ' . random_int(1, 32000),
            'entity_id_title' => 'Gonazales Delivery',
            'vendor_id_title' => 'Carrier',
            'reserve_account_vendor_id_title' => '1',
            'vendor_reserve_code' => '',
            'min_balance' => '0',
            'contribution_amount' => '10',
            'initial_balance' => '10',
            'current_balance' => '1000',
            'submit' => 'Save',
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

    /**
     * @depends testEditActionNewAccount
     */
    public function testEditActionNotValid(array $data)
    {
        $lastRACIdBefore = self::$rAE->getCollection()
            ->getLastItem()
            ->getId();
        $data['description'] = '';
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $lastRACIdAfter = self::$rAE->getCollection()
            ->getLastItem()
            ->getId();
        $this->assertEquals($lastRACIdBefore, $lastRACIdAfter);
    }

    public function testEditActionGetNoId()
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

    /**
     * @depends testEditActionNewAccount
     */
    public function testEditActionShow(array $data)
    {
        $data['reserve_account_id'] = self::$rAE->load($data['description'], 'description')
            ->getId();
        $RAC = new Application_Model_Entity_Accounts_Reserve_Contractor();
        $data['id'] = $RAC->load($data['reserve_account_id'], 'reserve_account_id')
            ->getId();

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => ['id' => $data['reserve_account_id']],
            ]
        );
        return $data;
    }

    /**
     * @depends testEditActionShow
     */
    public function testEditActionEditReserveAccount(array $data)
    {
        $data['description'] = $data['description'] . ' EDIT';

        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );
        $description = self::$rAE->load($data['reserve_account_id'])
            ->getDescription();
        $this->assertEquals($description, $data['description']);
        return $data;
    }

    /**
     * @depends testEditActionEditReserveAccount
     */
    public function testDeleteAction(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'delete'],
                'get' => ['id' => $data['id']],
            ]
        );
        $deleted = self::$rAE->load($data['reserve_account_id'])
            ->getDeleted();
        $this->assertEquals($deleted, Application_Model_Entity_System_SystemValues::DELETED_STATUS);
    }

    /**
     * @depends testEditActionEditReserveAccount
     */
    public function testAddBeforeMultiAction(array $data)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'post' => $data,
            ]
        );

        return $data;
    }

    //    /**
    //     * @depends testEditActionEditReserveAccount
    //     */
    //    public function testMultiAction(array $data)
    //    {
    //        $this->baseTestAction(
    //            array(
    //                'params' => array('action' => 'multiaction'),
    //                'ajax'   => array('ids'    => $data['id']),
    //            )
    //        );
    //    }

    /**
     * @depends testEditActionEditReserveAccount
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

    public function testNewSetupAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => ['setup' => '1'],
            ]
        );
    }

    public function testNewZeroSetupAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'edit'],
                'get' => ['setup' => '0'],
            ]
        );
    }

    public function testUpdateracollectionAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'updateracollection'],
                'ajax' => [
                    'vendorEntityId' => (new Application_Model_Entity_Entity_Vendor())->getCollection()
                        ->getLastItem()
                        ->getEntityId(),
                ],
            ]
        );
    }

    public function testUpdateracollectionGet()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'updateracollection'],
                'get' => [],
            ]
        );
    }

    public function testUpdaterasetupAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'updaterasetup'],
                'ajax' => [
                    'raId' => (new Application_Model_Entity_Accounts_Reserve_Vendor())->getCollection()
                        ->getLastItem()
                        ->getEntityId(),
                ],
            ]
        );
    }

    public function testUpdaterasetupGet()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'updaterasetup'],
                'get' => [],
            ]
        );
    }

    public function testUpdatecarriervendorAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'updatecarriervendor'],
                'ajax' => [
                    'contractor-id' => (new Application_Model_Entity_Entity_Contractor())->getCollection()
                        ->getLastItem()
                        ->getEntityId(),
                ],
            ]
        );
    }

    public function testUpdatecarriervendorGet()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'updatecarriervendor'],
                'get' => [],
            ]
        );
    }
}
