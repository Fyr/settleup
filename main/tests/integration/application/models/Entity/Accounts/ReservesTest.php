<?php

class Entity_Accounts_ReservesTest extends BaseTestCase
{
    protected static $_reserveAccount;
    protected static $_reserveTransaction;
    protected static $_reserveAccountCarrier;
    protected static $_reserveAccountVendor;

    public function testReserveAccount()
    {
        Application_Model_Entity_Accounts_User::login(16);
        self::$_reserveAccount = (new Application_Model_Entity_Accounts_Reserve())->setData(
            [
                'entity_id' => '',
            ]
        )
            ->save();
    }

    public function testReserveAccountCarrierSave()
    {
        Application_Model_Entity_Accounts_User::login(16);
        self::$_reserveAccountCarrier = (new Application_Model_Entity_Accounts_Reserve_Carrier())->setData(
            [
                'reserve_account_id' => self::$_reserveAccount->getId(),
            ]
        )
            ->save();
    }

    public function testReserveAccountCarrierLoad()
    {
        Application_Model_Entity_Accounts_User::login(16);
        self::$_reserveAccountCarrier->load('1');
        self::$_reserveAccountCarrier->getDefaultValues();
    }

    public function testReserveAccountCarrierSetPriority()
    {
        Application_Model_Entity_Accounts_User::login(16);
        (new Application_Model_Entity_Accounts_Reserve_Carrier())->setData(
            [
                'reserve_account_id' => self::$_reserveAccount->getId(),
            ]
        )
            ->save();
        self::$_reserveAccountCarrier->setPriority(['1', '2']);
    }

    public function testReserveAccountCarrierGetCarrierCollection()
    {
        self::$_reserveAccountCarrier->getCarrierCollection();
        $id = (new Application_Model_Entity_Accounts_User())->getCollection()
            ->addFilter('role_id', '2')
            ->getFirstItem()
            ->getId();
        Application_Model_Entity_Accounts_User::login($id);
        self::$_reserveAccountCarrier->getCarrierCollection();
    }

    public function testReserveAccountTransaction()
    {
        Application_Model_Entity_Accounts_User::login(16);
        self::$_reserveTransaction = (new Application_Model_Entity_Accounts_Reserve_Transaction())->getCollection()
            ->getLastItem();
        self::$_reserveTransaction->setData('source_id', '0')
            ->save();
        self::$_reserveTransaction->setData(
            'type',
            Application_Model_Entity_System_ReserveTransactionTypes::CASH_ADVANCE
        )
            ->save();
        self::$_reserveTransaction->setData('created_by', null)
            ->save();
        self::$_reserveTransaction->getDefaultData();
    }

    public function testReserveAccountTransactionGetSender()
    {
        self::$_reserveTransaction->getSender();
    }

    public function testReserveAccountTransactionGetReceiver()
    {
        self::$_reserveTransaction->getReceiver();
    }

    public function testReserveAccountTransactionGetProviderName()
    {
        self::$_reserveTransaction->getProviderName();
    }

    public function testReserveAccountTransactionGetAmountWithSign()
    {
        self::$_reserveTransaction->getAmountWithSign();
    }

    public function testReserveAccountTransactionGetAmountWithSignWithdrawal()
    {
        self::$_reserveTransaction->setData(
            'type',
            Application_Model_Entity_System_ReserveTransactionTypes::WITHDRAWAL
        )
            ->save();
        self::$_reserveTransaction->getAmountWithSign();
    }

    public function testReserveAccountVendorGetVendorCollection()
    {
        Application_Model_Entity_Accounts_User::login(16);
        self::$_reserveAccountVendor = (new Application_Model_Entity_Accounts_Reserve_Vendor())->getCollection()
            ->getFirstItem();
        self::$_reserveAccountVendor->getVendorCollection();
    }

    public function testReserveAccountVendorGetVendorCollectionUserVendor()
    {
        $userVendor = (new Application_Model_Entity_Accounts_User())->getCollection()
            ->addFilter('role_id', '4')
            ->getFirstItem();
        Application_Model_Entity_Accounts_User::login($userVendor->getId());
        self::$_reserveAccountVendor->getVendorCollection();
    }

    public function testReserveAccountVendorGetCarrierCollection()
    {
        Application_Model_Entity_Accounts_User::login(16);
        self::$_reserveAccountVendor = (new Application_Model_Entity_Accounts_Reserve_Vendor())->getCollection()
            ->getFirstItem();
        self::$_reserveAccountVendor->getCarrierCollection();
    }

    public function testReserveAccountVendorGetCarrierUserCarrier()
    {
        $userVendor = (new Application_Model_Entity_Accounts_User())->getCollection()
            ->addFilter('role_id', '2')
            ->getFirstItem();
        Application_Model_Entity_Accounts_User::login($userVendor->getId());
        self::$_reserveAccountVendor->getCarrierCollection();
    }

    public function testResourceAccountsReserveCarrier()
    {
        $model = new Application_Model_Resource_Accounts_Reserve_Carrier();
        $this->assertEquals(is_array($model->getInfoFields()), true);
    }

    //    public function testReserveAccountContractorDelete()
    //    {
    //        $_reserveAccountContractor = (new Application_Model_Entity_Accounts_Reserve_Contractor())->setData(array(
    //                'reserve_account_id' => self::$_reserveAccount->getId(),
    //                'reserve_account_vendor_id' => (new Application_Model_Entity_Accounts_Reserve_Vendor())
    //                        ->getCollection()->getFirstItem()->getId()
    //            )
    //        )->save();
    //        $_reserveAccountContractor->delete();
    //    }
    //
    //    public function testReserveAccountVendorDelete()
    //    {
    //        self::$_reserveAccountVendor->delete();
    //    }
    //
    //    public function testReserveAccountCarrierDelete()
    //    {
    //        self::$_reserveAccountCarrier->delete();
    //    }

}
