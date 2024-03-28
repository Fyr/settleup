<?php


class Application_Model_Entity_Collection_Powerunit_Powerunit extends Application_Model_Base_Collection
{
    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Application_Model_Entity_Powerunit_Powerunit(),
            'contractor_id',
            new Application_Model_Entity_Entity_Contractor(),
            'entity_id',
            [
                'entity_id',
                'contractor_code' => 'code',
                'tax_id',
                'company_name',
                'first_name',
                'last_name',
                'department',
                'route',
            ]
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_Contractor(),
            'status',
            new Application_Model_Entity_System_ContractorStatus(),
            'id',
            ['status_title' => 'title']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_Contractor(),
            'correspondence_method',
            new Application_Model_Entity_Entity_Contact_Type(),
            'id',
            ['correspondence_method_title' => 'title']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_Contractor(),
            'settlement_group_id',
            new Application_Model_Entity_Settlement_Group(),
            'id',
            ['settlement_group_division_id' => 'division_id']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Settlement_Group(),
            'division_id',
            new Application_Model_Entity_Entity_Carrier(),
            'id',
            ['division_id' => 'id', 'division_code' => 'short_code']
        );

        return $this;
    }

    /**
     * get collection of not deleted entities
     *
     * @return $this
     */
    public function addDivisionFilter()
    {
        $this->addFilter(
            'carrier_id',
            Application_Model_Entity_Accounts_User::getCurrentUser()->getEntity()->getEntityId()
        );

        return $this;
    }

    public function addFilterByCarrierContractor($carrierId = null)
    {
        if (!$carrierId) {
            $carrier = Application_Model_Entity_Accounts_User::getCurrentUser()->getSelectedCarrier();
            if ($carrier->getId()) {
                $carrierId = $carrier->getEntityId();
            } else {
                return $this->getEmptyCollection();
            }
        }

        $this->addFilter('carrier_id', $carrierId);

        return $this;
    }

    public function addFilterByVendorVisibility($onlyActive = true)
    {
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();
        if ($userEntity->isVendor()) {
            $entity = $userEntity->getEntity();
            if ($onlyActive) {
                $status = [
                    Application_Model_Entity_System_VendorStatus::STATUS_ACTIVE,
                ];
            } else {
                $status = [
                    Application_Model_Entity_System_VendorStatus::STATUS_ACTIVE,
                    Application_Model_Entity_System_VendorStatus::STATUS_RESCINDED,
                ];
            }
            $contractorsId = (new Application_Model_Entity_Entity_ContractorVendor())->getCollection()->addFilter(
                'vendor_id',
                $entity->getEntityId()
            )->addFilter(
                'status',
                $status,
                'IN'
            )->getField('contractor_id');
            if (!$contractorsId) {
                $contractorsId = [0];
            }
            $this->addFilter('entity_id', $contractorsId, 'IN');
        }

        return $this;
    }

    public function addConfiguredFilter()
    {
        $this->addFilter('status', Application_Model_Entity_System_ContractorStatus::STATUS_NOT_CONFIGURED, '!=');

        return $this;
    }

    public function addSettlementGroupFilter()
    {
        $contractorCollection = (new Application_Model_Entity_Entity_Contractor())
            ->getCollection()
            ->addSettlementGroupFilter();

        $contractorIds = [];
        foreach ($contractorCollection as $contractor) {
            $contractorIds[] = $contractor->getEntityId();
        }

        if (empty($contractorIds)) {
            $contractorIds = [0];
        }

        $this->addFilter(
            'contractor_id',
            $contractorIds,
            'IN'
        );

        return $this;
    }
}
