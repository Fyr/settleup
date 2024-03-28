<?php

class Application_Model_Entity_Collection_Accounts_User extends Application_Model_Base_Collection
{
    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Application_Model_Entity_Accounts_User(),
            'entity_id',
            new Application_Model_Entity_Entity(),
            'id',
            ['entity_name' => 'name']
        );

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

    public function addModeratorFilter()
    {
        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        if ($user->isModerator()) {
            $this->addFilter(
                'role_id',
                [
                    Application_Model_Entity_System_UserRoles::MODERATOR_ROLE_ID,
                    Application_Model_Entity_System_UserRoles::SUPER_ADMIN_ROLE_ID,
                ],
                'NOT IN',
                true,
                Application_Model_Base_Collection::WHERE_TYPE_AND,
                true
            );
            $this->addFilter('id', $user->getId(), '=', true, Application_Model_Base_Collection::WHERE_TYPE_OR, true);
        }

        return $this;
    }
}
