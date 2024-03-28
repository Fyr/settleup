<?php

class Entity_Collection_TransactionsTest extends BaseTestCase
{
    public function testEntityCollectionSettlementRule()
    {
        $entity = (new Application_Model_Entity_Transactions_Disbursement());
        $collection = new Application_Model_Entity_Collection_Transactions_Disbursement($entity);
        $collection->load();
        $user = $this->getUserByRole(3);
        Application_Model_Entity_Accounts_User::login($user->getId());
        $collection->addCarrierFilter();
    }
}
