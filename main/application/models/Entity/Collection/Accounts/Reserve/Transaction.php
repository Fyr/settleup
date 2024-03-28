<?php

use Application_Model_Entity_Accounts_Reserve as AccountsReserve;
use Application_Model_Entity_Accounts_Reserve_Transaction as EntityTransaction;
use Application_Model_Entity_Accounts_Reserve_Vendor as ReserveVendor;
use Application_Model_Entity_Entity as Entity;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Powerunit_Powerunit as PowerUnit;
use Application_Model_Entity_Settlement_Cycle as Cycle;
use Application_Model_Entity_System_ReserveTransactionTypes as ReserveTransactionTypes;

class Application_Model_Entity_Collection_Accounts_Reserve_Transaction extends Application_Model_Base_Collection
{
    use Application_Model_Entity_Collection_SettlementFilterTrait;
    use Application_Model_Entity_Collection_ContractorFilterTrait;

    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new EntityTransaction(),
            'type',
            new ReserveTransactionTypes(),
            'id',
            ['title', 'type_priority']
        );

        $this->addFieldsForSelect(
            new EntityTransaction(),
            'reserve_account_vendor',
            new AccountsReserve(),
            'id',
            ['entity_id', 'account_name']
        );

        $this->addFieldsForSelect(
            new AccountsReserve(),
            'id',
            new ReserveVendor(),
            'reserve_account_id',
            ['vendor_reserve_code' => 'vendor_reserve_code']
        );

        $this->addFieldsForSelect(
            new EntityTransaction(),
            'contractor_id',
            new Contractor(),
            'entity_id',
            ['company_name', 'contractor_code' => 'code']
        );

        $this->addFieldsForSelect(
            new Contractor(),
            'entity_id',
            new PowerUnit(),
            'contractor_id',
            ['powerunit_code' => 'code', 'powerunit_deleted' => 'deleted']
        );

        $this->addFieldsForSelect(
            new EntityTransaction(),
            'settlement_cycle_id',
            new Cycle(),
            'id',
            ['settlement_cycle_status' => 'status_id', 'cycle_start_date', 'cycle_close_date']
        );

        $this->addFieldsForSelect(
            new AccountsReserve(),
            'entity_id',
            new Entity(),
            'id',
            ['vendor_name' => 'name', 'vendor_entity_id' => 'id']
        );

        return $this;
    }

    /**
     * return $amount - $adjuctedBalance
     *
     * @return float
     */
    public function getAffectedAmount()
    {
        $amount = 0;
        $adjuctedBalance = 0;
        foreach ($this as $transaction) {
            $amount += $transaction->getAmount();
            $adjuctedBalance += $transaction->getAdjustedBalance();
        }

        return $amount - $adjuctedBalance;
    }

    /**
     * Filters reserve transactions collection by currently selected carrier
     *
     * @return Application_Model_Entity_Collection_Payments_Payment
     */
    public function addCarrierFilter()
    {
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();
        $currentCycles = $userEntity->getEntity()->getCurrentCarrier()->getCycles()->getField('id');
        if ($currentCycles) {
            $this->addFilter('settlement_cycle_id', $currentCycles, 'IN');
        } else {
            $this->addFilter('settlement_cycle_id', ["0"], 'IN');
        }

        if ($userEntity->isVendor()) {
            $this->addFilter('vendor_entity_id', $userEntity->getEntityId());
        }

        return $this;
    }

    public function addNonDeletedFilter($deletedFieldName = null)
    {
        // If we apply filter for just 'deleted'
        // unfortunately it is applied for powerunit instead of main table (why???) (((
        // so we have to specify table name explicitly to avoid a bug
        $tableName = $this->_entity->getResource()->getTableName();
        $this->addFilter($tableName.'.deleted', "0");

        return $this;
    }
}
