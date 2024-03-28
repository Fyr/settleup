<?php


class Application_Model_Grid_Reporting_ReserveAccountVendor extends Application_Model_Grid
{
    public function __construct()
    {
        $reserveAccountVendorEntity = new Application_Model_Entity_Accounts_Reserve_Vendor();

        $customFilters = [
            'addNonDeletedFilter',
            ['name' => 'addCarrierVendorFilter', 'value' => true],
        ];

        $header = [
            'header' => $reserveAccountVendorEntity->getResource()->getInfoFieldsForReport(),
            'sort' => ['priority' => 'ASC'],
            'dragrows' => false,
            'id' => static::class,
            'filter' => true,
            'checkboxField' => 'reserve_account_id',
            'idField' => 'id',
            'titleField' => 'account_name',
            'pagination' => false,
            'ignoreMassactions' => true,
        ];

        $grid = parent::__construct(
            $reserveAccountVendorEntity::class,
            $header,
            [],
            $customFilters,
            [],
            []
        );

        return $grid;
    }
}
