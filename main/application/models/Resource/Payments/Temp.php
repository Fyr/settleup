<?php

use Application_Model_Entity_Accounts_User as User;

class Application_Model_Resource_Payments_Temp extends Application_Model_Base_Resource
{
    protected $_name = 'payments_temp';

    public function getInfoFields()
    {
        $fieldNames = User::getCurrentUser()->getSelectedCarrier()->getCustomFieldNames();

        return [
            'contractor_code' => 'Contractor Code',
            'compensation_code' => 'Compensation Code',
            'payment_code' => $fieldNames->getPaymentCode(),
            'powerunit_code' => 'Power Unit',
            'carrier_payment_code' => $fieldNames->getCarrierPaymentCode(),
            'description' => $fieldNames->getDescription(),
            'category' => $fieldNames->getCategory(),
            'department' => $fieldNames->getDepartment(),
            'gl_code' => $fieldNames->getGlCode(),
            'shipment_complete_date' => 'Shipment Complete Date',
            'reference' => 'Reference',
            'taxable' => 'Taxable',
            'driver' => 'Driver',
            'loaded_miles' => 'Loaded Miles',
            'empty_miles' => 'Empty Miles',
            'origin_city' => 'Origin City/State',
            'destination_city' => 'Destination City/State',
            'invoice' => $fieldNames->getInvoice(),
            'invoice_date' => $fieldNames->getInvoiceDate(),
            'disbursement_code' => $fieldNames->getDisbursementCode(),
            'disbursement_date' => 'Disbursement Date',
            'quantity' => 'Qty',
            'rate' => 'Rate',
            'title' => 'Status',
        ];
    }

    /**
     * @return Application_Model_Entity_Payments_Payment
     */
    public function getParentEntity()
    {
        return new Application_Model_Entity_Payments_Payment();
    }
}
