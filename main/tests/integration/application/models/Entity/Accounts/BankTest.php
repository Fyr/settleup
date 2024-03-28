<?php

class Entity_Accounts_BankTest extends BaseTestCase
{
    protected static $_bankAccount;

    public function testBakAccountHistory()
    {
        $history = (new Application_Model_Entity_Accounts_Bank_History())->setData(
            [
                'bank_account_id' => self::$_bankAccount->getId(),
                'payment_type' => '1',
                'ACH_bank_routing_id' => '1234565',
            ]
        )
            ->save();
        $history->hideBankAccountNumber();
    }

    public function testBankAccountGetDefaultValuesContractor()
    {
        self::$_bankAccount->setData(
            'entity_id',
            (new Application_Model_Entity_Entity())->getCollection()
                ->addFilter('entity_type_id', '2')
                ->getFirstItem()
                ->getId()
        )
            ->save();
        self::$_bankAccount->getDefaultValues();
    }

    public function testBankAccountGetDefaultValuesVendor()
    {
        self::$_bankAccount->setData(
            'entity_id',
            (new Application_Model_Entity_Entity())->getCollection()
                ->addFilter('entity_type_id', '3')
                ->getFirstItem()
                ->getId()
        )
            ->save();
        self::$_bankAccount->getDefaultValues();
    }

    public function testBankAccountGetCarrierCollection()
    {
        $userCarrierId = (new Application_Model_Entity_Accounts_User())->getCollection()
            ->addFilter('role_id', '2')
            ->getFirstItem()
            ->getId();
        Application_Model_Entity_Accounts_User::login($userCarrierId);
        self::$_bankAccount->getVendorCollection();
        self::$_bankAccount->getCarrierCollection();
    }

    public function testBankAccountGetVendorCollection()
    {
        $userVendorId = (new Application_Model_Entity_Accounts_User())->getCollection()
            ->addFilter('role_id', '4')
            ->getFirstItem()
            ->getId();
        Application_Model_Entity_Accounts_User::login($userVendorId);
        self::$_bankAccount->getVendorCollection();
        self::$_bankAccount->getCarrierCollection();
    }

    public function testBankAccountHasReserveAccount()
    {
        self::$_bankAccount->hasReserveAccount();
    }

    public static $temp;

    public function testModelAccountsBankTemp()
    {
    }
}
