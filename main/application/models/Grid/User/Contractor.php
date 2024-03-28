<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Entity_Permissions as Permissions;

class Application_Model_Grid_User_Contractor extends Application_Model_Grid
{
    public function __construct()
    {
        $contractorEntity = new Contractor();

        $contractorsHeader = [
            'header' => [
                'code' => 'ID',
                'company_name' => 'Company',
                'first_name' => 'First Name',
                'last_name' => 'Last Name',
            ],
            'sort' => ['entity_id' => 'ASC'],
            'titleField' => $contractorEntity->getTitleColumn(),
            'filter' => true,
            'id' => static::class,
            'pagination' => false,
            'ignoreMassactions' => true,
            'checkboxField' => false,
            'idField' => 'entity_id',
        ];

        $user = User::getCurrentUser();
        if ($user->isCarrier() && $user->hasPermission(Permissions::CONTRACTOR_USER_CREATE)) {
            $customFilters = ['addCarrierFilter', 'vendorFilter'];
        } else {
            $customFilters = ['addNonDeletedFilter'];
        }

        return parent::__construct(
            $contractorEntity::class,
            $contractorsHeader,
            false,
            $customFilters
        );
    }
}
