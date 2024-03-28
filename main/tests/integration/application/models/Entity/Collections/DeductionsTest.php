<?php

class Entity_Collection_DeductionsTest extends BaseTestCase
{
    public function testDeductionsCollectionDeductions()
    {
        $entity = (new Application_Model_Entity_Deductions_Deduction());
        $collection = new Application_Model_Entity_Collection_Deductions_Deduction($entity);
        $collection->load();

        $userRoles = [1, 2, 3, 4];
        foreach ($userRoles as $role) {
            Application_Model_Entity_Accounts_User::login(
                $this->getUserByRole($role)
                    ->getId()
            );
            $collection->addCarrierFilter();
        }
        Application_Model_Entity_Accounts_User::login(16);
        $collection->addCarrierFilter();
        $collection->getAffectedAmount();
    }

    public function testDeductionsCollectionDeductionsSetup()
    {
        $entity = (new Application_Model_Entity_Deductions_Setup());
        $collection = new Application_Model_Entity_Collection_Deductions_Setup($entity);
        $collection->load();

        $userRoles = [1, 2, 3, 4];
        foreach ($userRoles as $role) {
            Application_Model_Entity_Accounts_User::login(
                $this->getUserByRole($role)
                    ->getId()
            );
            $collection->addCarrierFilter();
            $collection->addUserVisibilityFilter();
        }
    }

    //    public function testDeductionsCollectionDeductionsRecurring()
    //    {
    //        $entity = (new Application_Model_Entity_Deductions_Recurring());
    //        $collection = new Application_Model_Entity_Collection_Deductions_Recurring($entity);
    //        $collection->load();
    //        Application_Model_Entity_Accounts_User::login(16);
    //        $collection->addCarrierFilter();
    //    }

    public function testDeductionsCollectionTemp()
    {
        $entity = (new Application_Model_Entity_Deductions_Temp());
        $collection = new Application_Model_Entity_Collection_Deductions_Temp($entity);
        $collection->load();
    }
}
