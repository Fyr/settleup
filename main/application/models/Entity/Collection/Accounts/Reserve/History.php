<?php

class Application_Model_Entity_Collection_Accounts_Reserve_History extends Application_Model_Base_Collection
{
    use Application_Model_Entity_Collection_SettlementFilterTrait;
    use Application_Model_Entity_Collection_ContractorFilterTrait;

    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Application_Model_Entity_Accounts_Reserve_History(),
            'reserve_account_id',
            new Application_Model_Entity_Accounts_Reserve(),
            'id',
            [
                'description',
                'min_balance',
                'contribution_amount',
                'contractor_id' => 'entity_id',
                'allow_negative',
            ]
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Accounts_Reserve_History(),
            'reserve_account_id',
            new Application_Model_Entity_Accounts_Reserve_Contractor(),
            'reserve_account_id',
            [
                'reserve_account_vendor_id',
                'reserve_account_contractor_id' => 'id',
                'contractor_vendor_reserve_code' => 'vendor_reserve_code',
            ]
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Accounts_Reserve_History(),
            'settlement_cycle_id',
            new Application_Model_Entity_Settlement_Cycle(),
            'id',
            [
                'cycle_start_date',
                'cycle_close_date',
            ]
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Accounts_Reserve_Contractor(),
            'reserve_account_vendor_id',
            new Application_Model_Entity_Accounts_Reserve_Vendor(),
            'id',
            ['vendor_reserve_code', 'vendor_reserve_account_id' => 'reserve_account_id']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Accounts_Reserve_Vendor(),
            'reserve_account_id',
            new Application_Model_Entity_Accounts_Reserve(),
            'id',
            ['vendor_entity_id' => 'entity_id'],
            'reserve_account_vendor.reserve_account_id=reserve_account_2.id'
        );

        $this->addFieldsForSelect(
            'reserve_account_2',
            'entity_id',
            new Application_Model_Entity_Entity(),
            'id',
            ['vendor_name' => 'name']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Accounts_Reserve(),
            'entity_id',
            new Application_Model_Entity_Powerunit_Powerunit(),
            'contractor_id',
            ['powerunit_code' => 'code', 'powerunit_deleted' => 'deleted']
        );

        return $this;
    }

    public function addNonDeletedFilter($deletedFieldName = null)
    {
        $this->addFilter('powerunit_deleted', 0);

        return $this;
    }
}
