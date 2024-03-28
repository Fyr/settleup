<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Payments_Setup as Setup;
use Application_Model_Entity_Powerunit_Powerunit as Powerunit;
use Application_Model_Entity_System_CyclePeriod as CyclePeriod;
use Application_Model_Entity_System_RecurringTitle as RecurringTitle;
use Application_Model_Entity_System_SetupLevels as SetupLevels;
use Application_Model_Entity_System_VendorStatus as Status;

class Application_Model_Entity_Collection_Payments_Setup extends Application_Model_Base_Collection
{
    /**
     * @return $this|void
     */
    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Setup(),
            'recurring',
            new RecurringTitle(),
            'id',
            ['recurring_title' => 'title']
        );

        $this->addFieldsForSelect(
            new Setup(),
            'billing_cycle_id',
            new CyclePeriod(),
            'id',
            ['billing_title' => 'title']
        );

        $this->addFieldsForSelect(
            new Setup(),
            'contractor_id',
            new Contractor(),
            'entity_id',
            ['contractor_name' => 'company_name', 'contractor_code' => 'code', 'carrier_status_id']
        );

        $this->addFieldsForSelect(
            new Setup(),
            'level_id',
            new SetupLevels(),
            'id',
            ['level_title' => 'title']
        );

        $this->addFieldsForSelect(
            new Setup(),
            'powerunit_id',
            new Powerunit(),
            'id',
            ['power_unit_code' => 'code']
        );

        return $this;
    }

    /**
     * Filters payments setup collection by currently selected carrier
     *
     * @param null $carrierId
     * @return $this
     */
    public function addCarrierFilter($carrierId = null)
    {
        if (!isset($carrierId)) {
            $carrierId = User::getCurrentUser()->getEntity()->getCurrentCarrier()->getEntityId();
        }
        $this->addFilter('carrier_id', $carrierId);

        return $this;
    }

    /**
     * @return $this
     */
    public function addMasterFilter()
    {
        $this->addFilter('level_id', SetupLevels::MASTER_LEVEL_ID);

        return $this;
    }

    /**
     * @param null $contractorId
     * @return $this
     */
    public function addFilterByEntityId($contractorId = null)
    {
        $this->addFilter('contractor_id', $contractorId);
        $this->vendorFilter();

        return $this;
    }

    public function vendorFilter()
    {
        $this->addFilter('carrier_status_id', [Status::STATUS_ACTIVE, Status::STATUS_RESCINDED], 'IN');

        return $this;
    }
}
