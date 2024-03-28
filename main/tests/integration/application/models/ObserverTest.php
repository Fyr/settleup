<?php

class ObserverTest extends BaseTestCase
{
    public function testObserverChangeInitialBalance()
    {
        $entity = (new Application_Model_Entity_Accounts_Reserve_Contractor())->getCollection()
            ->getFirstItem();
        $observer = new Application_Model_Observes_ChangeInitialBalance($entity, 'reject');
        $entity->setId('');
        $observer->doUpdate($entity);
    }

    public function testObserverChangebalanceobserver()
    {
        $entity = (new Application_Model_Entity_Settlement_Cycle())->getCollection()
            ->getFirstItem();
        $observer = new Application_Model_Observes_Changebalanceobserver($entity, 'reject');
        $subject = (new Application_Model_Entity_Accounts_Reserve_Contractor());
        $subject = $subject->getCollection()
            ->getFirstItem();
        $observer->doUpdate($subject);
    }

    public function testObserverChangestatusobserverActiveStatusNoTerminationDate()
    {
        $entity = (new Application_Model_Entity_Entity_Contractor())->getCollection()
            ->getFirstItem();
        $entity->setData('status', Application_Model_Entity_System_ContractorStatus::STATUS_ACTIVE);
        $entity->status = Application_Model_Entity_System_ContractorStatus::STATUS_ACTIVE;
        $observer = new Application_Model_Observes_Changestatusobserver($entity, 'reject');
        $observer->doUpdate($entity);
    }

    public function testObserverChangestatusobserverActiveStatus()
    {
        $entity = (new Application_Model_Entity_Entity_Contractor())->getCollection()
            ->getFirstItem();
        $entity->setData('status', Application_Model_Entity_System_ContractorStatus::STATUS_ACTIVE);
        $entity->status = Application_Model_Entity_System_ContractorStatus::STATUS_ACTIVE;
        $entity->setData('termination_date', 'value');
        $observer = new Application_Model_Observes_Changestatusobserver($entity, 'reject');
        $observer->doUpdate($entity);
    }

    public function testObserverChangestatusobserverTerminatedStatus()
    {
        $entity = (new Application_Model_Entity_Entity_Contractor())->getCollection()
            ->getFirstItem();
        $entity->setData('status', Application_Model_Entity_System_ContractorStatus::STATUS_TERMINATED);
        $entity->status = Application_Model_Entity_System_ContractorStatus::STATUS_TERMINATED;
        $entity->setData('termination_date', 'value');
        $observer = new Application_Model_Observes_Changestatusobserver($entity, 'reject');
        $observer->doUpdate($entity);
    }

    public function testObserverDeleteEntityObserver()
    {
        $visibility = (new Application_Model_Entity_Accounts_UsersVisibility());
        $entity = (new Application_Model_Entity_Entity_Contractor())->getCollection()
            ->getFirstItem();
        $visibility->setData(
            [
                'entity_id' => $entity->getEntityId(),
                'participant_id' => '1',
            ]
        )
            ->save();
        $observer = new Application_Model_Observes_DeleteEntityObserver($entity, 'reject');
        $observer->doUpdate($entity);
    }

    public function testObserverDeleteEntityObserverElse()
    {
        $visibility = (new Application_Model_Entity_Accounts_UsersVisibility());
        $entity = (new Application_Model_Entity_Entity_Contractor())->getCollection()
            ->getFirstItem();
        $visibility->setData(
            [
                'entity_id' => '1',
                'participant_id' => $entity->getEntityId(),
            ]
        )
            ->save();
        $observer = new Application_Model_Observes_DeleteEntityObserver($entity, 'reject');
        $observer->doUpdate($entity);
    }

    //    public function testObserverRejectSettlementCycle()
    //    {
    //        $transaction = (new Application_Model_Entity_Accounts_Reserve_Transaction())->getCollection()->getLastItem();
    //        $transaction->setData('settlement_cycle_id','1')->save();
    //        $entity = (new Application_Model_Entity_Settlement_Cycle())->load('1');
    //        $observer = new Application_Model_Observes_RejectSettlementCycle($entity ,'reject');
    //        $observer->doUpdate($entity);
    //    }

    public function testObserverSaveDeductionsByPriority()
    {
        $entity = (new Application_Model_Entity_Deductions_Deduction())->getCollection()
            ->getFirstItem();
        $observer = new Application_Model_Observes_SaveDeductionsByPriority($entity, 'reject');
        $observer->doUpdate($entity);

        $mock = $this->getMock('Application_Model_Entity_Deductions_Deduction');
        $mock->expects($this->any())
            ->method('getPriority')
            ->will($this->returnValue(false));
        $observer = new Application_Model_Observes_SaveDeductionsByPriority($mock, 'reject');
        $observer->doUpdate($mock);
    }

    public function testObserverSaveGlobalDeduction()
    {
        $user = (new Application_Model_Entity_Accounts_User())->load(16);
        Application_Model_Entity_Accounts_User::login(16);
        $user->setData('last_selected_carrier', '1001')
            ->save();
        $entity = (new Application_Model_Entity_Deductions_Deduction())->getCollection()
            ->getFirstItem();
        $observer = new Application_Model_Observes_SaveGlobalDeduction($entity, 'reject');
        $observer->doUpdate($entity);
        $mock = $this->getMock('Application_Model_Entity_Deductions_Deduction');
        $mock->expects($this->any())
            ->method('getContractorId')
            ->will($this->returnValue(false));
        $observer = new Application_Model_Observes_SaveGlobalDeduction($mock, 'reject');
        $observer->doUpdate($mock);
    }

    public function testObserverSaveGlobalPayment()
    {
        $entity = (new Application_Model_Entity_Payments_Payment())->getCollection()
            ->getFirstItem();
        $observer = new Application_Model_Observes_SaveGlobalPayment($entity, 'reject');
        $observer->doUpdate($entity);
        $mock = $this->getMock('Application_Model_Entity_Payments_Payment');
        $mock->expects($this->any())
            ->method('getContractorId')
            ->will($this->returnValue(false));
        $mock->expects($this->any())
            ->method('getSetup')
            ->will(
                $this->returnValue(
                    (new Application_Model_Entity_Payments_Setup())->getCollection()
                        ->getFirstItem()
                )
            );
        $observer = new Application_Model_Observes_SaveGlobalPayment($mock, 'reject');
        $observer->doUpdate($mock);
    }

    public function testObserverDisbursementSenderObserver()
    {
        $entityId = (new Application_Model_Entity_Transactions_Disbursement())->getCollection()
            ->getFirstItem()
            ->getEntityId();
        $subject = (new Application_Model_Entity_Entity())->load($entityId)
            ->getEntityTypeID();
        $object = new Application_Model_Entity_Entity_Carrier();
        if ($subject == 2) {
            $object = new Application_Model_Entity_Entity_Contractor();
        }
        if ($subject == 3) {
            $object = new Application_Model_Entity_Entity_Vendor();
        }
        $object = $object->load($entityId, 'entity_id');
        $observer = new Application_Model_Observes_DisbursementSenderObserver($object, 'reject');
        $observer->doUpdate($object);
    }
}
