<?php

use Application_Model_Entity_Accounts_Reserve as AccountsReserve;
use Application_Model_Entity_Accounts_Reserve_Transaction as EntityTransaction;
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

        // pull transaction type
        $this->addFieldsForSelect(
            new EntityTransaction(),
            'type',
            new ReserveTransactionTypes(),
            'id',
            ['type' => 'title']
        );

        // pull contractor data
        $this->addFieldsForSelect(
            new EntityTransaction(),
            'contractor_id',
            new Contractor(),
            'entity_id',
            ['contractor_code' => 'code']
        );

        // pull RA data
        $this->addFieldsForSelect(
            new EntityTransaction(),
            'reserve_account_contractor',
            new AccountsReserve(),
            'id',
            ['ra_name' => 'account_name', 'ra_code' => 'code']
        );

        // pull power unit data
        $this->addFieldsForSelect(
            new AccountsReserve(),
            'powerunit_id',
            new PowerUnit(),
            'id',
            ['powerunit_code' => 'code']
        );

        // pull settlement cycle data
        $this->addFieldsForSelect(
            new EntityTransaction(),
            'settlement_cycle_id',
            new Cycle(),
            'id',
            ['settlement_cycle_status' => 'status_id', 'cycle_start_date', 'cycle_close_date']
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
        $currentCycles = $userEntity->getEntity()->getCycles()->getField('id');
        if ($currentCycles) {
            $this->addFilter('settlement_cycle_id', $currentCycles, 'IN');
        } else {
            $this->addFilter('settlement_cycle_id', ["0"], 'IN');
        }

        if ($userEntity->isOnboarding()) {
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
