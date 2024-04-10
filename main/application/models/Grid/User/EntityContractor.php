<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Entity_Permissions as Permissions;

class Application_Model_Grid_User_EntityContractor extends Application_Model_Grid
{
    public function __construct()
    {
        $contractorEntity = new Contractor();

        $contractorsHeader = [
            'header' => [
                'carrier_name' => 'Division',
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
        if ($user->isManager() && $user->hasPermission(Permissions::CONTRACTOR_USER_CREATE)) {
            $customFilters = ['addCarrierFilter', 'vendorFilter', 'addCarrierName'];
        } else {
            $customFilters = ['addNonDeletedFilter', 'addCarrierName', 'addCarrierNonDeletedFilter'];
        }
        $this->setData('row_data', ['carrier_id' => 'carrier-id', 'carrier_name' => 'carrier-name']);

        return parent::__construct(
            $contractorEntity::class,
            $contractorsHeader,
            false,
            $customFilters
        );
    }
}
