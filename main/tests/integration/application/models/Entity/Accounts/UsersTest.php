<?php

class Entity_Accounts_UsersTest extends BaseTestCase
{
    protected static $_accountUser;

    public function testAccountsUser()
    {
        $this->loginUser();
        self::$_accountUser = (new Application_Model_Entity_Accounts_User())->setData(
            [
                'email' => random_int(1, 32000),
                'password' => 'ppp',
                'name' => 'test',
            ]
        )
            ->save();
        self::$_accountUser->setRoleId('3')
            ->save();
        self::$_accountUser->getEntity();
        self::$_accountUser->setRoleId('4')
            ->save();
        self::$_accountUser->getEntity();
        self::$_accountUser->setRoleId()
            ->save();
        self::$_accountUser->getEntity();
    }

    //    public function testAccountUserGetCurrentCycle()
    //    {
    //        $user = (new Application_Model_Entity_Accounts_User)->load('16');
    //        $cycle = (new Application_Model_Entity_Settlement_Cycle())->load('1');
    //        Application_Model_Entity_Accounts_User::login(16);
    //        $this->assertFalse(self::$_accountUser->getCurrentCycle());
    //        $this->getRequest()->setCookie('settlement_cycle_id','1');
    //        $user->setLastSelectedCarrier('2')->save();
    //        $this->assertFalse(self::$_accountUser->getCurrentCycle());
    //        $user->setLastSelectedCarrier('1')->save();
    //        $this->assertEquals(self::$_accountUser->getCurrentCycle(),$cycle);
    //    }

    public function testAccountUserGetDefaultValues()
    {
        self::$_accountUser->getDefaultValues();
    }

    public function testAccountUserGetRealIp()
    {
        $this->assertEquals($_SERVER['REMOTE_ADDR'], self::$_accountUser->getRealIp());
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '127.0.0.2';
        $this->assertEquals($_SERVER['HTTP_X_FORWARDED_FOR'], self::$_accountUser->getRealIp());
        $_SERVER['HTTP_CLIENT_IP'] = '127.0.0.3';
        $this->assertEquals($_SERVER['HTTP_CLIENT_IP'], self::$_accountUser->getRealIp());
    }

    public function testAccountUserEncode()
    {
        $this->assertEquals(
            '7ec8d9d3db4f8cc9a7b08f9e9b2252ba',
            self::$_accountUser->encodePassword('1QdfR24vf')
        );
    }

    public function testAccountUserLoginNull()
    {
        (new Application_Model_Entity_Accounts_User())->login(null);
    }

    //    public function testAccountUserGetSelectedEntity()
    //    {
    //        $accountUser = (new Application_Model_Entity_Accounts_User)->setData(array(
    //                'email' => rand(1,32000),
    //                'password' => 'ppp',
    //                'name' => 'test'
    //            )
    //        )->save();
    //        $mock = $this->getMock('Application_Model_Entity_Accounts_User');
    //        $mock::staticExpects($this->any())->method('getSelectedContractor')->will($this->returnValue(null));
    //        $mock::staticExpects($this->any())->method('getSelectedCarrier')->will($this->returnValue(null));
    //        $mock->getSelectedEntity();
    //
    //        self::$_accountUser->setLastSelectedContractor()->setLastSelectedCarrier()->save();
    //        Application_Model_Entity_Accounts_User::login(self::$_accountUser->getId());
    //        self::$_accountUser->getSelectedEntity();
    //        self::$_accountUser->setLastSelectedContractor('1')->save();
    //        self::$_accountUser->getSelectedEntity();
    //    }

    public function testSaveWithPasswordUserDeleted()
    {
        self::$_accountUser->setDeleted(Application_Model_Entity_System_SystemValues::NOT_DELETED_STATUS)
            ->save();
        self::$_accountUser->setPassword('pass')
            ->save();
        $this->assertNull(self::$_accountUser->getPassword());
        self::$_accountUser->setDeleted(Application_Model_Entity_System_SystemValues::DELETED_STATUS)
            ->save();
        $this->assertEquals('', self::$_accountUser->getPassword());
        $this->assertEquals('', self::$_accountUser->getEmail());
    }

    //    public function testAccountUserGetSelectedCarrierCarrier()
    //    {
    //        $carrier = $this->newCarrier();
    //        self::$_accountUser = $this->newUser(['entity_id' => $carrier->getEntityId()]);
    //        self::$_accountUser->setRoleId('2')->setEntityId()->save();
    //        self::$_accountUser->updateRestData();
    //        $this->loginUser(self::$_accountUser->getId(), $this->defaultPassMd5);
    //        self::$_accountUser->getSelectedCarrier();
    //        self::$_accountUser->setRoleId('3')->save();
    //        self::$_accountUser->updateRestData();
    //        $this->loginUser(self::$_accountUser->getId(), $this->defaultPassMd5);
    //        self::$_accountUser->getSelectedCarrier();
    //        self::$_accountUser->setRoleId()->save();
    //        self::$_accountUser->getSelectedCarrier();

    //        self::$_accountUser= (new Application_Model_Entity_Accounts_User)->getCollection()->addFilter('role_id','2')->getFirstItem();
    //        Application_Model_Entity_Accounts_User::login(self::$_accountUser->getId());
    //        self::$_accountUser->getSelectedCarrier();
    //    }
    //
    //    public function testAccountUserGetSelectedCarrierContractor()
    //    {
    //
    //    }

    //    public function testAccountUserGetSelectedCarrierVendor()
    //    {
    //        self::$_accountUser= (new Application_Model_Entity_Accounts_User)->getCollection()->addFilter('role_id','4')->getFirstItem();
    //        Application_Model_Entity_Accounts_User::login(self::$_accountUser->getId());
    //        self::$_accountUser->getSelectedCarrier();
    //    }

    //    /**
    //     * @var Application_Model_Entity_Accounts_UsersVisibility
    //     */
    //    private static $_userVisibility;

    //    public function testUserVisibility()
    //    {
    //        self::$_userVisibility = (new Application_Model_Entity_Accounts_UsersVisibility)->getCollection()->getFirstItem();
    //    }

    //    public function testUserVisibilityAddUserCarrier()
    //    {
    //        $user = (new Application_Model_Entity_Accounts_User())->load('2','role_id');
    //        self::$_userVisibility->addEntities($user->getId(),array('1'));
    //        self::$_userVisibility->userEntityId = $user->getEntityId();
    //        self::$_userVisibility->entities = array(
    //            'Contractors' =>
    //                new Application_Model_Entity_Entity_Contractor(),
    //            'Vendors' => new Application_Model_Entity_Entity_Vendor(),
    //        );
    //        self::$_userVisibility->getDefaultValues();
    //    }
    //
    //    public function testUserVisibilityAddUserVendor()
    //    {
    //        $user = (new Application_Model_Entity_Accounts_User())->load('3','role_id');
    //        self::$_userVisibility->addEntities($user->getId(),array('1'));
    //        self::$_userVisibility->userEntityId = $user->getEntityId();
    //        self::$_userVisibility->getDefaultValues();
    //    }

}
