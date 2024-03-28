<?php

class Entity_Collection_AccountsTest extends BaseTestCase
{
    public function testAccountsCollectionAccountsReserve()
    {
        $entity = (new Application_Model_Entity_Accounts_Reserve_Carrier());
        $collection = new Application_Model_Entity_Collection_Accounts_Reserve_Carrier($entity);
        $collection->_beforeLoad();
        $collection->addVisibilityFilterForUser();

        $userRoles = [1, 2, 3, 4];
        foreach ($userRoles as $role) {
            Application_Model_Entity_Accounts_User::login(
                $this->getUserByRole($role)
                    ->getId()
            );
            $collection->addVisibilityFilterForUser();
        }
    }

    public function testAccountsCollectionAccountsReserveTransactions()
    {
        $entity = (new Application_Model_Entity_Accounts_Reserve_Transaction());
        $collection = new Application_Model_Entity_Collection_Accounts_Reserve_Transaction($entity);
        $collection->load();
        Application_Model_Entity_Accounts_User::login(
            $this->getUserByRole(1)
                ->getId()
        );
        $collection->getAffectedAmount();
        $collection->addCarrierFilter();
    }

    public function testAccountsCollectionAccountsReserveVendor()
    {
        $entity = (new Application_Model_Entity_Accounts_Reserve_Vendor());
        $collection = new Application_Model_Entity_Collection_Accounts_Reserve_Vendor($entity);
        $collection->load();
        $userRoles = [1, 2, 3, 4];
        foreach ($userRoles as $role) {
            Application_Model_Entity_Accounts_User::login(
                $this->getUserByRole($role)
                    ->getId()
            );
            $collection->addVisibilityFilterForUser();
            $collection->addCarrierVendorFilter();
        }
        Application_Model_Entity_Accounts_User::login(
            $this->getUserByRole(3)
                ->getId()
        );
        $collection->addVisibilityFilterForUser();
        $collection->addCarrierFilter();
        $collection->addVendorFilter();
        $collection->getDeletedFieldName();
        $collection->addNonDeletedFilter();
        $collection->addFilterByEntity(1);
        $user16 = (new Application_Model_Entity_Accounts_User())->load(16);
        $user16->setLastSelectedContractor(1)
            ->save();
        Application_Model_Entity_Accounts_User::login(16);
        $collection->addCarrierVendorFilter();
    }
}
