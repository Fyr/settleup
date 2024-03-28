<?php

class Application_Model_Entity_Collection_Settlement_Cycle extends Application_Model_Base_Collection
{
    public function addFilterByUserRole()
    {
        $this->addCarrierFilter();

        return $this;
    }

    /**
     * Filters settlement cycle collection by currently selected carrier
     *
     * @return Application_Model_Entity_Collection_Settlement_Cycle
     */
    public function addCarrierFilter()
    {
        $user = Application_Model_Entity_Accounts_User::getCurrentUser();

        $this->addFilter('carrier_id', $user->getCarrierEntityId());
        $this->addNonDeletedFilter();

        return $this;
    }

    /**
     * Filters settlement cycle collection by currently selected settlement group
     *
     * @return Application_Model_Entity_Collection_Settlement_Cycle
     */
    public function addSettlementGroupFilter()
    {

        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        $settlementGroupId = $user->getLastSelectedSettlementGroup();

        $this->addFilter('settlement_group_id', $settlementGroupId);
        $this->addNonDeletedFilter();

        return $this;
    }

    /**
     * @param null $id
     * @return null| Application_Model_Entity_Settlement_Cycle
     */
    public function getActiveCycle($id = null)
    {
        $this->setOrder('cycle_start_date', 'ASC');
        $items = $this->getItems();

        if ($id && isset($items[$id])) {
            return $items[$id];
        } else {
            foreach ($items as $cycle) {
                if ($cycle->getStatusId() < Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID) {
                    return $cycle;
                }
            }
        }

        return new Application_Model_Entity_Settlement_Cycle();
    }

    //    public function getCycleByFilter($filterId)
    //    {
    //        $items = $this->getItems();
    //        if ($id && isset($items[$id])) {
    //            return $items[$id];
    //        } else {
    //            foreach ($items as $cycle) {
    //                if ($cycle->getStatusId() < Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID) {
    //                    return $cycle;
    //                }
    //            }
    //        }
    //        return new Application_Model_Entity_Settlement_Cycle();
    //    }

    /**
     * Filters settlement cycle collection by currently selected carrier
     * and verified status
     *
     * @return Application_Model_Entity_Collection_Settlement_Cycle
     */
    public function addVerifiedFilter()
    {
        $this->addCarrierFilter()->addSettlementGroupFilter()->addFilter(
            'status_id',
            Application_Model_Entity_System_SettlementCycleStatus::VERIFIED_STATUS_ID
        );

        return $this;
    }

    /**
     * Filters settlement cycle collection by currently selected carrier
     * and closed status
     *
     * @return Application_Model_Entity_Collection_Settlement_Cycle
     */
    public function addClosedFilter()
    {
        $this->addCarrierFilter()->addSettlementGroupFilter()->addFilter(
            'status_id',
            Application_Model_Entity_System_SettlementCycleStatus::APPROVED_STATUS_ID
        );

        return $this;
    }

    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Application_Model_Entity_Settlement_Cycle(),
            'status_id',
            new Application_Model_Entity_System_SettlementCycleStatus(),
            'id',
            ['title']
        );

        return $this;
    }
}
