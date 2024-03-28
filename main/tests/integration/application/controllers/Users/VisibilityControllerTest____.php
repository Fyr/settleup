<?php

class Users_VisibilityControllerTest extends BaseTestCase
{
    /** @var Users_VisibilityController */
    private $controller;

    protected function setUp(): void
    {
        $this->setDefaultController('users_visibility');
        parent::setUp();
    }

    public function testIndexAction()
    {
        $userCarrierId = (new Application_Model_Entity_Accounts_User())->getCollection()
            ->addFilter('role_id', Application_Model_Entity_System_UserRoles::CARRIER_ROLE_ID)
            ->getFirstItem()
            ->getId();
        Application_Model_Entity_Accounts_User::login($userCarrierId);
        $this->baseTestAction(
            [
                'params' => ['action' => 'index'],
                'get' => ['userEntityId' => '1'],
                'assert' => ['action' => 'list'],
            ],
            false
        );
    }

    public function testListAction()
    {
        $userVendorId = (new Application_Model_Entity_Accounts_User())->getCollection()
            ->addFilter('role_id', Application_Model_Entity_System_UserRoles::VENDOR_ROLE_ID)
            ->getFirstItem()
            ->getId();
        Application_Model_Entity_Accounts_User::login($userVendorId);
        $this->baseTestAction(
            [
                'params' => ['action' => 'list'],
                'get' => [
                    'userEntityId' => '1',
                    'isAjax' => 'true',
                ],
            ],
            false
        );
    }

    public function testDeleteAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'delete'],
                'get' => [
                    'userEntityId' => '1',
                    'participantId' => '2',
                ],
                'assert' => ['action' => 'list'],
            ]
        );
    }

    public function testAddselectedItemsActionGet()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'addselecteditems'],
                'get' => [],
            ]
        );
    }

    public function testAddselectedItemsAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'addselecteditems'],
                'ajax' => [
                    'selectedItemsId' => ['2'],
                    'userEntityId' => '1',
                ],
                'assert' => ['action' => 'list'],
            ]
        );
    }
}
