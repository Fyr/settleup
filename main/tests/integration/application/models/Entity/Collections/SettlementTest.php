<?php

class Entity_Collection_SettlementTest extends BaseTestCase
{
    public function testEntityCollectionSettlementCycle()
    {
        $entity = (new Application_Model_Entity_Settlement_Cycle());
        $collection = new Application_Model_Entity_Collection_Settlement_Cycle($entity);
        $collection->load();

        $userRoles = [1, 2, 3, 4];
        foreach ($userRoles as $role) {
            Application_Model_Entity_Accounts_User::login(
                $this->getUserByRole($role)
                    ->getId()
            );
            $collection->addFilterByUserRole();
        }
        $collection->addClosedFilter();
    }

    //    public function testEntityCollectionSettlementRule()
    //    {
    //        $entity = (new Application_Model_Entity_Settlement_Rule());
    //        $collection = new Application_Model_Entity_Collection_Settlement_Rule($entity);
    //        $collection->load();
    //    }

}
