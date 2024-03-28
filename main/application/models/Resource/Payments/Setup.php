<?php

use Application_Model_Entity_Accounts_User as User;

class Application_Model_Resource_Payments_Setup extends Application_Model_Base_Resource
{
    protected $_name = 'payment_setup';

    public function getInfoFields()
    {
        $fieldNames = User::getCurrentUser()->getSelectedCarrier()->getCustomFieldNames();

        return [
            'id' => 'ID',
            'payment_code' => $fieldNames->getPaymentCode(),
            'power_unit_code' => 'Power Unit Code',
            'description' => $fieldNames->getDescription(),
            'category' => $fieldNames->getCategory(),
            'recurring_title' => 'Recurring',
            'billing_title' => 'Frequency',
            'taxable' => 'Taxable',
            'level_title' => 'Level',
            'quantity' => 'Qty',
            'rate' => 'Rate',
        ];
    }

    public function getInfoFieldsIndividual()
    {
        $fieldNames = User::getCurrentUser()->getSelectedCarrier()->getCustomFieldNames();

        return [
            'contractor_code' => 'ID',
            'contractor_name' => 'Contractor',
            'payment_code' => $fieldNames->getPaymentCode(),
            'description' => $fieldNames->getDescription(),
            'category' => $fieldNames->getCategory(),
            'recurring_title' => 'Recurring',
            'billing_title' => 'Frequency',
            'quantity' => 'Qty',
            'rate' => 'Rate',
        ];
    }

    public function getInfoFieldsForPopup(): array
    {
        $fieldNames = User::getCurrentUser()->getSelectedCarrier()->getCustomFieldNames();

        return [
            'payment_code' => $fieldNames->getPaymentCode(),
            'description' => $fieldNames->getDescription(),
            'category' => $fieldNames->getCategory(),
            'department' => $fieldNames->getDepartment(),
            'recurring_title' => 'Recurring',
            'billing_title' => 'Frequency',
            'quantity' => 'Qty',
            'rate' => 'Rate',
        ];
    }
}
