<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Accounts_UserEntity as UserEntity;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Entity_Vendor as Vendor;

class Application_Model_Entity_Collection_Accounts_UserEntity extends Application_Model_Base_Collection
{
    public function addCarrierFilter()
    {
        //$user = Application_Model_Entity_Accounts_User::getCurrentUser();
        //TODO will be change in SUP-1170
        /*if ($user->isManager()) {
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
        }*/

        return $this;
    }

    public function addFilterByUserId(?int $userId = null): self
    {
        $userId = $userId ?: User::getCurrentUser()->getId();

        $this->addFilter('user_id', $userId);

        return $this;
    }

    public function addDivisionsInfo(): self
    {
        $this->addFieldsForSelect(
            new UserEntity(),
            'entity_id',
            new Application_Model_Entity_Entity_Carrier(),
            'entity_id',
            ['division_entity_id' => 'id']
        );

        return $this;
    }
}
