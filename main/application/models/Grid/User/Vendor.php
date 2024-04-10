<?php

class Application_Model_Grid_User_Vendor extends Application_Model_Grid
{
    public function __construct()
    {
        $vendorEntity = new Application_Model_Entity_Entity_Vendor();

        $vendorsHeader = [
            'header' => $vendorEntity->getResource()->getInfoFieldsForPopup(),
            'sort' => ['entity_id' => 'ASC'],
            'titleField' => $vendorEntity->getTitleColumn(),
            'filter' => true,
            'id' => static::class,
            'pagination' => false,
            'ignoreMassactions' => true,
            'checkboxField' => false,
            'idField' => 'entity_id',
        ];

        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        if ($user->isManager() && $user->hasPermission(
            Application_Model_Entity_Entity_Permissions::VENDOR_USER_CREATE
        )) {
            $customFilters = [['name' => 'addVisibilityFilterForUser', 'value' => [true, true]]];
        } else {
            $customFilters = ['addNonDeletedFilter'];
        }

        return parent::__construct(
            $vendorEntity::class,
            $vendorsHeader,
            false,
            $customFilters
        );
    }
}
