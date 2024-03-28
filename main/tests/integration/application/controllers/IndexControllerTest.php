<?php

class IndexControllerTest extends BaseTestCase
{
    /** @var IndexController */
    private $controller;

    protected function setUp(): void
    {
        $this->setDefaultController('index');
        parent::setUp();
    }

    public function testIndexAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'index'],
                'get' => [],
            ]
        );
    }

    public function testSearchautocompleteAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'searchautocomplete'],
                'get' => [],
            ]
        );
    }

    public function testSearchHintStubAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'searchhintstub'],
                'ajax' => ['query' => 'qwerty asdf'],
            ]
        );
    }

    public function testSearchResultStubAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'searchresultstub'],
                'ajax' => ['data' => '0'],
            ]
        );
    }

    public function testChangeCurrentCarrierAction()
    {
        $carrierId = (new Application_Model_Entity_Entity_Carrier())->getCollection()
            ->getLastItem()
            ->getId();
        $this->baseTestAction(
            [
                'params' => ['action' => 'changecurrentcarrier'],
                'ajax' => [
                    'selectedCarrierId' => $carrierId,
                    'currentController' => 'index',
                ],
            ]
        );
        $this->assertEquals(
            $carrierId,
            (new Application_Model_Entity_Accounts_User())->load(16)
                ->getLastSelectedCarrier()
        );
    }

    public function testChangeCurrentContractorAction()
    {
        $contractorId = (new Application_Model_Entity_Entity_Contractor())->getCollection()
            ->getLastItem()
            ->getId();
        $this->baseTestAction(
            [
                'params' => ['action' => 'changecurrentcontractor'],
                'ajax' => [
                    'selectedContractorId' => $contractorId,
                    'currentController' => 'index',
                ],
            ]
        );
        //        $user = (new Application_Model_Entity_Accounts_User())->load(16);
        //        $this->assertEquals($contractorId, $user->getLastSelectedContractor());
    }

    public function testChangeCurrentContractorActionNone()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'changecurrentcontractor'],
                'ajax' => [
                    'currentController' => 'bankaccounts_index',
                    'selected-entity-id' => 'none',
                ],
            ]
        );
        $user = (new Application_Model_Entity_Accounts_User())->load(16);
        $this->assertNull($user->getLastSelectedContractor());
    }

    public function testChangeCurrentContractorAction2()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'changecurrentcontractor'],
                'ajax' => [
                    'currentController' => 'bankaccounts_index',
                    'selected-entity-id' => '2',
                    'no-redirect' => 'true',
                ],
            ]
        );
    }
}
