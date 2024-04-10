<?php

use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_System_SetupLevels as SetupLevel;

/**
 * @method $this staticLoad($id, $field = null)
 * @method Application_Model_Entity_Collection_Deductions_Setup getCollection()
 * @method Application_Model_Resource_Deductions_Setup getResource()
 */
class Application_Model_Entity_Deductions_Setup extends Application_Model_Base_Entity
{
    // use Application_Model_Entity_PriorityTrait;
    use Application_Model_Entity_Permissions_CarrierVendorTrait;
    use Application_Model_RecurringTrait;
    use Application_Model_Recurring_UpdateNextRecurringTrait;

    /** @var Contractor */
    protected $contractor;

    // /**
    //  * Sets the priority
    //  *
    //  * @param $priorityArr - An array where ( key => value )
    //  * is (priority => id of record)
    //  */
    // public function setPriority($priorityArr)
    // {
    //     foreach ($priorityArr as $priority => $id) {
    //         $this->load($id);
    //         $this->addData(
    //             [
    //                 'priority' => $priority,
    //             ]
    //         );
    //         $this->save();
    //     }
    // }

    public function _afterSave()
    {
        parent::_afterSave();

        // $this->reorderPriority();

        return $this;
    }

    // public function reorderPriority($whereColumns = null)
    // {
    //     $collection = (new Application_Model_Entity_Deductions_Setup())->getCollection()->addUserVisibilityFilter(
    //         [false, false],
    //         true
    //     )->addNonDeletedFilter()->setOrder('priority', 'asc');

    //     if ($this->getLevelId() == SetupLevel::MASTER_LEVEL_ID) {
    //         $collection->addMasterFilter();
    //     } else {
    //         $collection->addFilterByEntityId($this->getContractorId());
    //     }

    //     $items = $collection->getItems();

    //     $items = array_values($items);

    //     foreach ($items as $priority => $item) {
    //         $prevItem = ($priority === 0) ? null : $items[$priority - 1];
    //         $nextItem = ($priority === (count($items) - 1)) ? null : $items[$priority + 1];
    //         if ($item->getData('priority') != $priority) {
    //             if ($this->getId() == $item->getId() && isset($prevItem) && $prevItem->getPriority(
    //             ) == $this->getPriority()) {
    //                 $this->updatePriority($prevItem->getId(), $priority);
    //             } elseif ($this->getId() == $item->getId(
    //             ) && $priority === 0 && isset($nextItem) && $nextItem == $this->getPriority()) {
    //                 $this->updatePriority($item->getId(), $priority + 1);
    //             } else {
    //                 $this->updatePriority($item->getId(), $priority);
    //             }
    //         }
    //     }

    //     // update individual templates
    //     if ($this->getLevelId() == SetupLevel::MASTER_LEVEL_ID) {
    //         if ($items) {
    //             foreach ($items as $item) {
    //                 $ids[] = $item->getId();
    //             }
    //             $ids = implode(',', $ids);

    //             $sql = "UPDATE deduction_setup i
    //                 LEFT JOIN deduction_setup m ON i.master_setup_id = m.id
    //                 LEFT JOIN contractor c ON i.contractor_id = c.entity_id
    //                 SET i.priority = m.priority
    //                 WHERE i.deleted = 0 AND m.deleted = 0 AND c.deduction_priority = 1 AND i.master_setup_id IN ($ids)";

    //             $this->getResource()->getAdapter()->query($sql);
    //         }
    //     }
    //     $this->reorderIndividualTemplates();

    //     return $this;
    // }

    protected function _beforeSave()
    {
        if ($this->getReserveAccountSender() == '') {
            $this->setReserveAccountSender();
        }
        if ($this->getReserveAccountReceiver() == '') {
            $this->setReserveAccountReceiver();
        }
        if ($this->getContractorId() == '') {
            $this->setContractorId();
        }

        // if (!$this->getId()) {
        //     $lastPrioritySetup = $this->getCollection()->addUserVisibilityFilter()->AddNonDeletedFilter()->setOrder(
        //         'priority',
        //         'DESC'
        //     )->addLimit(1)->getFirstItem();
        //     if ($lastPrioritySetup && $lastPrioritySetup->getId()) {
        //         $this->setData('priority', $lastPrioritySetup->getPriority() + 1);
        //     } else {
        //         $this->setData('priority', 0);
        //     }
        // }

        if ($this->getBillingCycleId() == Application_Model_Entity_System_CyclePeriod::BIWEEKLY_PERIOD_ID) {
            $this->setFirstStartDay((new DateTime($this->getBiweeklyStartDay()))->format('w'));
        }

        if (!$this->getLevelId()) {
            $this->setLevelId(SetupLevel::MASTER_LEVEL_ID);
        }
        if ($this->getRate()) {
            $this->setRate(str_replace(',', '', (string) $this->getRate()));
        }

        return $this;
    }

    /**
     * Adds to the dataset of model related titles for the form
     *
     * @return Application_Model_Entity_Deductions_Setup
     */
    public function getDefaultValues()
    {
        $this->setProviderIdTitle($this->_getProviderIdTitle());
        $this->setReserveAccountReceiverTitle(
            $this->_getReserveAccountVendorTitle()
        );

        return $this;
    }

    /**
     * Returns title by provider_id
     *
     * @return string
     */
    protected function _getProviderIdTitle()
    {
        $carrierEntity = new Application_Model_Entity_Entity_Carrier();
        $vendorEntity = new Application_Model_Entity_Entity_Vendor();
        $entityEntity = new Application_Model_Entity_Entity();
        $entityEntity->load($this->getProviderId(), 'id');

        if ($entityEntity->getEntityTypeId() == Application_Model_Entity_Entity_Type::TYPE_DIVISION) {
            $serchingEntity = $carrierEntity;
        } else {
            $serchingEntity = $vendorEntity;
        }

        $serchingEntity->load($this->getProviderId(), 'entity_id');

        return $serchingEntity->getData($serchingEntity->getTitleColumn());
    }

    /**
     * Returns title by reserv_account_receiver
     *
     * @return string
     */
    protected function _getReserveAccountVendorTitle()
    {
        $title = '';
        if ($this->getReserveAccountReceiver() > 0) {
            $reserveAccountVendorEntity = new Application_Model_Entity_Accounts_Reserve_Vendor();
            $reserveAccountEntity = new Application_Model_Entity_Accounts_Reserve();
            $reserveAccountEntity->load($this->getReserveAccountReceiver());
            $title = $reserveAccountEntity->getData(
                $reserveAccountVendorEntity->getTitleColumn()
            );
        }

        return $title;
    }

    /**
     * @return Application_Model_Entity_Entity()
     */
    public function getProvider()
    {
        $entity = new Application_Model_Entity_Entity();

        return $entity->load($this->getProviderId());
    }

    /**
     * Returns carrier collection in accordance with current user role
     *
     * @return Application_Model_Entity_Collection_Entity_Carrier
     */
    public function getCarrierCollection()
    {
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();
        $carrierEntity = new Application_Model_Entity_Entity_Carrier();
        $carrierCollection = $carrierEntity->getCollection();

        if ($userEntity->getRoleId() == Application_Model_Entity_System_UserRoles::MANAGER_ROLE_ID) {
            $entityId = Application_Model_Entity_Entity::getCurrentEntity()->getId();
            $carrierCollection->addFilter('entity_id', $entityId);
        } else {
            $carrierCollection->addVisibilityFilterForUser();
        }

        return $carrierCollection;
    }

    /**
     * Returns vendor collection in accordance with current user role
     *
     * @return Application_Model_Entity_Collection_Entity_Vendor
     */
    public function getVendorCollection()
    {
        return (new Application_Model_Entity_Entity_Vendor())->getCollection();
    }

    public function getBillingCycleOptions()
    {
        $billingCycles = new Application_Model_Entity_System_CyclePeriod();
        $billingCycles = $billingCycles->getBillingCycles(
            $this->getBillingCycleId()
        );

        return $billingCycles;
    }

    public function getRecurringType()
    {
        return $this->getBillingCycleId();
    }

    public function deleteIndividualTemplates()
    {
        $this->getResource()->update(['deleted' => 1], ['master_setup_id = ?' => $this->getId()]);

        return $this;
    }

    public function createMasterTemplate(array $data): self
    {
        $entity = new self();
        $entity->setData([
            'provider_id' => $data['provider_id'],
            'level_id' => SetupLevel::MASTER_LEVEL_ID,
            'deduction_code' => $data['deduction_code'],
            'description' => $data['description'],
            'category' => $data['category'],
            'department' => $data['department'],
            'disbursement_code' => $data['disbursement_code'],
            'billing_cycle_id' => $data['billing_cycle_id'],
            'first_start_day' => $data['first_start_day'],
            'second_start_day' => $data['second_start_day'],
            'quantity' => 1,
            'rate' => $data['rate'],
            'recurring' => $data['recurring'],
        ]);
        $entity->save();

        return $entity;
    }

    public function createIndividualTemplates()
    {
        $id = $this->getId();
        $stmt = $this->getResource()->getAdapter()->prepare('CALL createIndividualDeductionTemplates(?)');
        $stmt->bindParam(1, $id);
        $stmt->execute();

        return $this;
    }

    public function updateIndividualTemplates()
    {
        $this->getResource()->update([
            'description' => $this->getData('description'),
            'category' => $this->getData('category'),
            'department' => $this->getData('department'),
            'gl_code' => $this->getData('gl_code'),
            'disbursement_code' => $this->getData('disbursement_code'),
            'terms' => $this->getData('terms'),
            'quantity' => $this->getData('quantity'),
            'rate' => $this->getData('rate'),
            'recurring' => $this->getData('recurring'),
            'billing_cycle_id' => $this->getData('billing_cycle_id'),
            'week_offset' => $this->getData('week_offset'),
            'first_start_day' => $this->getData('first_start_day'),
            'second_start_day' => $this->getData('second_start_day'),
            'biweekly_start_day' => $this->getData('biweekly_start_day'),
        ], [
            'master_setup_id = ?' => $this->getId(),
            'changed = 0',
        ]);

        return $this;
    }

    /**
     * @return Contractor
     */
    public function getContractor()
    {
        if (!$this->contractor || (int)$this->getContractorId() !== (int)$this->contractor->getEntityId()) {
            $this->contractor = Contractor::staticLoad((int)$this->getContractorId(), 'entity_id');
        }

        return $this->contractor;
    }

    public function getAssociatedEntity()
    {
        return new Application_Model_Entity_Deductions_Deduction();
    }

    public function reorderIndividualTemplates()
    {
        $carrierId = Application_Model_Entity_Accounts_User::getCurrentUser()->getCarrierEntityId();
        //     $sql = 'UPDATE deduction_setup ds
        //     LEFT JOIN
        //         (SELECT
        //         id, priority, new_priority, contractor_id FROM (
        //             SELECT
        //             @r:= IF(@u = contractor_id, @r + 1,0) AS new_priority,
        //             id,
        //             priority,
        //             @u:= contractor_id as contractor_id
        //             FROM (
        //                 SELECT ds.id, ds.priority, ds.contractor_id
        //                 FROM deduction_setup ds
        //                 LEFT JOIN contractor co ON ds.contractor_id = co.entity_id
        //                 LEFT JOIN entity ce ON ds.contractor_id = ce.id
        //                 LEFT JOIN entity ve ON ds.provider_id = ve.id
        //                 LEFT JOIN contractor_vendor cv ON co.entity_id = cv.contractor_id AND ds.provider_id = cv.vendor_id
        //                 WHERE ds.contractor_id IS NOT NULL
        //                     AND ds.deleted = 0
        //                     AND co.carrier_id = ?
        //                     AND ce.deleted = 0
        //                     AND ve.deleted = 0
        //                     AND (cv.status IN (0 , 2) OR ds.provider_id = co.carrier_id)
        //                     order by ds.contractor_id asc, ds.priority asc
        //                 ) as setups,
        //                 (SELECT @r:= 1) AS r,
        //                 (SELECT @u:= 0) AS u
        //             ) as priority
        //         WHERE priority != new_priority) priority ON ds.id = priority.id
        //     SET ds.priority = priority.new_priority
        //     WHERE ds.id = priority.id AND ds.contractor_id = priority.contractor_id;';
        // $this->getResource()->getAdapter()->query($sql, [$carrierId]);

        return $this;
    }
}
