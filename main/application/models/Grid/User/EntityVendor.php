<?php

class Application_Model_Grid_User_EntityVendor extends Application_Model_Grid
{
    public function __construct()
    {
        $vendorEntity = new Application_Model_Entity_Entity_Vendor();
        $infoFields = $vendorEntity->getResource()->getInfoFieldsForPopup();
        $infoFields = array_merge(['carrier_name' => 'Division'], $infoFields);

        $vendorsHeader = [
            'header' => $infoFields,
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
        if ($user->isCarrier() && $user->hasPermission(
            Application_Model_Entity_Entity_Permissions::VENDOR_USER_CREATE
        )) {
            $customFilters = [['name' => 'addVisibilityFilterForUser', 'value' => [true, true]], 'addCarrierName'];
        } else {
            $customFilters = ['addNonDeletedFilter', 'addCarrierName', 'addCarrierNonDeletedFilter'];
        }
        $this->setData('row_data', ['carrier_id' => 'carrier-id', 'carrier_name' => 'carrier-name']);

        return parent::__construct(
            $vendorEntity::class,
            $vendorsHeader,
            false,
            $customFilters
        );
    }
}
