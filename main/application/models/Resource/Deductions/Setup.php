<?php

class Application_Model_Resource_Deductions_Setup extends Application_Model_Base_Resource
{
    protected $_name = 'deduction_setup';

    /**
     * Returns an array of fields and their titles to be displayed in the grid
     *
     * @return array
     */
    public function getInfoFields()
    {
        $infoFields = [
            // 'priority' => 'Priority',
            'id' => 'ID',
            'provider_name' => 'Vendor',
            'power_unit_code' => 'Power Unit Code',
            'deduction_code' => 'Code',
            'description' => 'Description',
            'category' => 'Category',
            'recurring_title' => 'Recurring',
            'billing_title' => 'Frequency',
            'level_title' => 'Level',
            'quantity' => 'Qty',
            'rate' => 'Rate',
        ];

        return $infoFields;
    }

    /**
     * Returns an array of fields and their titles to be displayed in the grid
     *
     * @return array
     */
    public function getInfoFieldsIndividual()
    {
        $infoFields = [
            'contractor_name' => 'Company',
            'provider_name' => 'Vendor',
            'deduction_code' => 'Code',
            'description' => 'Description',
            'category' => 'Category',
            'recurring_title' => 'Recurring',
            'billing_title' => 'Frequency',
            'quantity' => 'Qty',
            'rate' => 'Rate',
        ];

        return $infoFields;
    }

    public function getInfoFieldsForPopup()
    {
        $infoFields = [
            'provider_name' => 'Vendor',
            'deduction_code' => 'Code',
            'description' => 'Description',
            'department' => 'Department',
            'recurring_title' => 'Recurring',
            'billing_title' => 'Frequency',
            'category' => 'Category',
            'quantity' => 'Qty',
            'rate' => 'Rate',
        ];

        return $infoFields;
    }
}
