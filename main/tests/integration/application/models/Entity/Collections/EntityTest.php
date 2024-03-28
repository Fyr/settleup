<?php

class Entity_Collection_EntityTest extends BaseTestCase
{
    public function testEntityCollectionContactInfo()
    {
        $entity = (new Application_Model_Entity_Entity_Contact_Info());
        $collection = new Application_Model_Entity_Collection_Entity_Contact_Info($entity);
        $collection->load();

        $collection->getZip();
        $collection->getState();
        $collection->getSecondAddress();
        $collection->getFirstAddress();
        $collection->getCity();
    }

    public function testEntityCollectionCarrier()
    {
        $entity = (new Application_Model_Entity_Entity_Carrier());
        $collection = new Application_Model_Entity_Collection_Entity_Carrier($entity);
        $collection->load();
        $userRoles = [1, 2, 3, 4];
        foreach ($userRoles as $role) {
            Application_Model_Entity_Accounts_User::login(
                $this->getUserByRole($role)
                    ->getId()
            );
            $collection->addVisibilityFilterForUser();
            $collection->addVisibilityFilter($role);
            $collection->addFilterByAddedEntities($role);
        }
        $collection->addCarrierVendorFilter();
        $collection->getDeletedFieldName();
    }

    public function testEntityCollectionContractor()
    {
        $entity = (new Application_Model_Entity_Entity_Contractor());
        $collection = new Application_Model_Entity_Collection_Entity_Contractor($entity);
        $collection->load();
        Application_Model_Entity_Accounts_User::login(16);

        $collection->addVisibilityFilterForUser(false);
        $collection->addVisibilityFilterForUser(true);
        $collection->addVisibilityFilterForCurrentCarrier();

        $collection->addFilterByAddedEntities(775);
        $collection->addFilterByAddedEntities(1);
        $collection->getDeletedFieldName();
    }

    public function testEntityCollectionContractorVendor()
    {
        $entity = (new Application_Model_Entity_Entity_ContractorVendor());
        $collection = new Application_Model_Entity_Collection_Entity_ContractorVendor($entity);
        $collection->load();
        $collection->addVendorFilter();
        $collection->filterByContractor(1);
        $collection->filterByVendor(1);
    }

    public function testEntityCollectionVendor()
    {
        $entity = (new Application_Model_Entity_Entity_Vendor());
        $collection = new Application_Model_Entity_Collection_Entity_Vendor($entity);
        $collection->load();

        $userRoles = [1, 2, 3, 4, -755];
        foreach ($userRoles as $role) {
            Application_Model_Entity_Accounts_User::login(
                $this->getUserByRole($role)
                    ->getId()
            );
            $collection->addVisibilityFilterForUser(1);
            $collection->addVisibilityFilterForUser();
            $collection->addFilterByAddedEntities($role);
        }
    }
}
