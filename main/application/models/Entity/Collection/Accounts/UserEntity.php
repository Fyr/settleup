<?php

use Application_Model_Entity_Accounts_UserEntity as UserEntity;
use Application_Model_Entity_Entity_Carrier as Carrier;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Entity_Vendor as Vendor;

class Application_Model_Entity_Collection_Accounts_UserEntity extends Application_Model_Base_Collection
{
    public function joinContractorTable()
    {
        $this->addFieldsForSelect(
            new UserEntity(),
            'entity_id',
            new Contractor(),
            'entity_id',
            ['carrier_id', 'code', 'first_name', 'last_name', 'company_name']
        );
        $this->addFieldsForSelect(
            new Contractor(),
            'carrier_id',
            new Carrier(),
            'entity_id',
            ['carrier_name' => 'name', 'carrier_entity_id' => 'id']
        );

        return $this;
    }

    public function joinVendorTable()
    {
        $this->addFieldsForSelect(
            new UserEntity(),
            'entity_id',
            new Vendor(),
            'entity_id',
            ['carrier_id', 'code', 'name']
        );
        $this->addFieldsForSelect(
            new Vendor(),
            'carrier_id',
            new Carrier(),
            'entity_id',
            ['carrier_name' => 'name', 'carrier_entity_id' => 'id']
        );

        return $this;
    }

    public function addCarrierFilter()
    {
        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        if ($user->isCarrier()) {
            $carrierId = $user->getEntityId();
            $this->addFieldsForSelect(
                new UserEntity(),
                'entity_id',
                new Vendor(),
                'entity_id',
                ['vendor_carrier_id' => 'carrier_id']
            );
            $this->addFieldsForSelect(
                new UserEntity(),
                'entity_id',
                new Contractor(),
                'entity_id',
                ['contractor_carrier_id' => 'carrier_id']
            );
            $this->addFilter('contractor_carrier_id', $carrierId, '=');
            $this->addFilter(
                'vendor_carrier_id',
                $carrierId,
                '=',
                true,
                Application_Model_Base_Collection::WHERE_TYPE_OR
            );
        }

        return $this;
    }
}
