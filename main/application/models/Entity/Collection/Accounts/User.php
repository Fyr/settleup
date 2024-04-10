<?php

class Application_Model_Entity_Collection_Accounts_User extends Application_Model_Base_Collection
{
    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Application_Model_Entity_Accounts_User(),
            'role_id',
            new Application_Model_Entity_System_UserRoles(),
            'id',
            ['role_title' => 'title']
        );

        return $this;
    }

    public function addCarrierFilter()
    {
        $user = Application_Model_Entity_Accounts_User::getCurrentUser();

        $entityIds = [0];

        if ($user->hasPermission(Application_Model_Entity_Entity_Permissions::PERMISSIONS_MANAGE)) {
            $entityIds[] = $user->getCarrierEntityId();
        }

        if ($user->hasPermission(Application_Model_Entity_Entity_Permissions::PERMISSIONS_MANAGE)) {
            $contractors = (new Application_Model_Entity_Entity_Contractor())->getCollection()->addFilter(
                'carrier_id',
                $user->getCarrierEntityId()
            )->addNonDeletedFilter()->getField('entity_id');
            $vendors = (new Application_Model_Entity_Entity_Vendor())->getCollection()->addFilter(
                'carrier_id',
                $user->getCarrierEntityId()
            )->addNonDeletedFilter()->getField('entity_id');
            $entityIds = array_merge($entityIds, $contractors, $vendors);
        }

        $this->addFilter('entity_id', $entityIds, 'IN');

        return $this;
    }

    public function addAdminFilter(): self
    {
        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        $this->addFilter(
            'role_id',
            [
                Application_Model_Entity_System_UserRoles::ADMIN_ROLE_ID,
                Application_Model_Entity_System_UserRoles::SUPER_ADMIN_ROLE_ID,
            ],
            'NOT IN',
            true,
            Application_Model_Base_Collection::WHERE_TYPE_AND,
            true
        );
        $this->addFilter('id', $user->getId(), '=', true, Application_Model_Base_Collection::WHERE_TYPE_OR, true);

        return $this;
    }

    public function addManagerFilter(): self
    {
        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        $this->addFilter(
            'role_id',
            [
                Application_Model_Entity_System_UserRoles::ADMIN_ROLE_ID,
                Application_Model_Entity_System_UserRoles::SUPER_ADMIN_ROLE_ID,
                Application_Model_Entity_System_UserRoles::MANAGER_ROLE_ID,
            ],
            'NOT IN',
            true,
            Application_Model_Base_Collection::WHERE_TYPE_AND,
            true
        );
        $this->addFilter('id', $user->getId(), '=', true, Application_Model_Base_Collection::WHERE_TYPE_OR, true);

        return $this;
    }

    public function addDivisionsInfo(): self
    {
        $this->addFieldsForSelect(
            new Application_Model_Entity_Accounts_User(),
            'id',
            new Application_Model_Entity_Accounts_UserEntity(),
            'user_id',
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Accounts_UserEntity(),
            'entity_id',
            new Application_Model_Entity_Entity_Carrier(),
            'entity_id',
            ['divisions' => 'GROUP_CONCAT(carrier.name SEPARATOR ", ")'],
        );

        $this->addGroup('users.id');

        return $this;
    }
}
