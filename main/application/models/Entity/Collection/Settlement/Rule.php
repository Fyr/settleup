<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity as Entity;
use Application_Model_Entity_Settlement_Cycle as Cycle;
use Application_Model_Entity_Settlement_Rule as Rule;
use Application_Model_Entity_System_CyclePeriod as CyclePeriod;

class Application_Model_Entity_Collection_Settlement_Rule extends Application_Model_Base_Collection
{
    /**
     * Filters settlement cycle collection by currently selected carrier
     *
     * @return Application_Model_Entity_Collection_Settlement_Rule
     */
    public function addCarrierFilter()
    {
        $carrierId = User::getCurrentUser()->getEntity()->getEntityId();
        $this->addFilter('carrier_id', $carrierId);

        return $this;
    }

    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Rule(),
            'carrier_id',
            new Entity(),
            'id',
            ['name', 'entity_deleted' => 'deleted']
        );

        $this->addFieldsForSelect(
            new Rule(),
            'cycle_period_id',
            new CyclePeriod(),
            'id',
            ['period_title' => 'title']
        );

        $this->addFieldsForSelect(
            new Rule(),
            'last_closed_cycle_id',
            new Cycle(),
            'id',
            ['cycle_start_date', 'cycle_close_date']
        );

        return $this;
    }

    public function addNonDeletedFilter($name = null)
    {
        $this->addFilter('deleted', 0);
        $this->addFilter('entity_deleted', 0);

        return $this;
    }
}
