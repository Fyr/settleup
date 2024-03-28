<?php

/**
 * @method $this staticLoad($id, $field = null)
 * @method Application_Model_Entity_Collection_Entity_Vendor getCollection()
 * @method Application_Model_Resource_Entity_Vendor getResource()
 */
class Application_Model_Entity_Entity_Vendor extends Application_Model_Entity_Entity_Base
{
    use Application_Model_ContactTrait;
    use Application_Model_Entity_Permissions_CarrierTrait;

    protected $_entityType = Application_Model_Entity_Entity_Type::TYPE_VENDOR;
    //TODO replace this property after refactoring
    protected $_titleColumn = 'name';

    //TODO Get off this method after refactoring
    public function getCarrier()
    {
        return $this->getCurrentCarrier();
    }

    /**
     * @return Application_Model_Entity_Entity_Carrier
     */
    public function getCurrentCarrier()
    {
        $carrierEntity = new Application_Model_Entity_Entity_Carrier();

        return $carrierEntity->load($this->getCarrierId(), 'entity_id');
    }

    /**
     * @return Application_Model_Entity_Entity_Contractor
     */
    public function getCurrentContractor()
    {
        $contractorEntity = new Application_Model_Entity_Entity_Contractor();
        $contractorId = Application_Model_Entity_Accounts_User::getCurrentUser()->getLastSelectedContractor();
        if ($contractorId) {
            return $contractorEntity->load($contractorId);
        }

        return null;
    }

    public function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getPriority()) {
            $this->setPriority($this->getMaxPriority() + 1);
        }
        if ($this->getDeleted() == Application_Model_Entity_System_SystemValues::DELETED_STATUS) {
            $this->removeRelatedData();
            $this->markColumnAsDeleted($this->colCode());
        }

        return $this;
    }

    public function getMaxPriority()
    {
        $priority = null;
        $collection = $this->getCollection();
        $collection->addVisibilityFilter(
            Application_Model_Entity_Entity::getCurrentEntity()->getId()
        );
        $collection->setOrder('vendor.priority');
        $vendor = $collection->getFirstItem();
        if ($vendor instanceof Application_Model_Entity_Entity_Vendor) {
            $priority = $vendor->getPriority();
        }

        return $priority;
    }

    /**
     * check deductions
     *
     * @return bool
     */
    public function hasDeductions()
    {
        $deductionEntity = new Application_Model_Entity_Deductions_Deduction();
        $select = $deductionEntity->getResource()->select()->where('provider_id = ?', $this->getEntityId())->where(
            'deleted = ?',
            0
        )->where('settlement_cycle_id IS NOT NULL')->limit(1);

        return (bool)$this->getResource()->getAdapter()->fetchRow($select);
    }

    public function removeRecurringDeductions()
    {
        $deductionEntity = new Application_Model_Entity_Deductions_Deduction();
        $select = 'provider_id = ' . $this->getEntityId() . ' AND deleted = 0 AND settlement_cycle_id IS NULL';
        $deductionEntity->getResource()->delete($select);

        return $this;
    }

    /**
     * check transactions
     *
     * @return bool
     */
    public function hasTransactions()
    {
        $select = $this->getResource()->getAdapter()->select()->from(['t' => 'reserve_transaction'], ['id'])->joinLeft(
            ['ra' => 'reserve_account'],
            't.reserve_account_vendor = ra.id'
        )->where('ra.entity_id = ?', $this->getEntityId())->where('t.deleted = ?', 0)->limit(1);

        return (bool)$this->getResource()->getAdapter()->fetchRow($select);
    }

    public function removeRelatedData()
    {
        $result = false;
        if ($id = $this->getEntityId()) {
            $sql = 'CALL removeVendorRelatedData(?)';
            $stmt = $this->getResource()->getAdapter()->prepare($sql);
            $stmt->bindParam(1, $id);
            $result = $stmt->execute();
        }

        return $result;
    }
}
