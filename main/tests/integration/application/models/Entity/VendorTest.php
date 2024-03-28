<?php

class VendorTest extends BaseTestCase
{
    /**
     * @var Application_Model_Entity_Entity_Vendor
     */
    protected static $_vendor;

    public function testVendor()
    {
        self::$_vendor = (new Application_Model_Entity_Entity_Vendor())->getCollection()
            ->getFirstItem();
        $this->assertTrue(is_object(self::$_vendor));
    }

    /**
     * @depends testVendor
     */
    public function testGetCarrier()
    {
        self::$_vendor->getCarrier();
    }

    public function testGetCurrentContractor()
    {
        $user = (new Application_Model_Entity_Accounts_User())->load(16);
        Application_Model_Entity_Accounts_User::login($user->getId());
        $user->setData('last_selected_contractor')
            ->save();
        self::$_vendor->getCurrentContractor();
        $user->setData('last_selected_contractor', '1002')
            ->save();
        self::$_vendor->getCurrentContractor();
    }

    public function testHasBankAccount()
    {
        self::$_vendor->hasBankAccount();
    }

    public function testGetMaxPriority()
    {
        $userVendor = (new Application_Model_Entity_Accounts_User())->getCollection()
            ->addFilter('role_id', 4)
            ->getFirstItem();
        Application_Model_Entity_Accounts_User::login($userVendor->getId());
        self::$_vendor->getMaxPriority();
    }
}
