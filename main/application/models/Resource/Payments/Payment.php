<?php

use Application_Model_Entity_Accounts_User as User;

class Application_Model_Resource_Payments_Payment extends Application_Model_Base_Resource
{
    protected $_name = 'payments';

    public function getInfoFields(): array
    {
        $fieldNames = User::getCurrentUser()->getSelectedCarrier()->getCustomFieldNames();

        return [
            'id' => 'ID',
            'payment_code' => str_replace(' ', '<br/>', (string) $fieldNames->getPaymentCode()),
            'contractor_code' => 'Contractor<br/>Code',
            'company_name' => 'Company',
            'powerunit_code' => 'Power Unit<br/>Code',
            'description' => $fieldNames->getDescription(),
            'category' => $fieldNames->getCategory(),
            'shipment' => 'Shipment',
            'shipment_complete_date' => 'Shipment<br/>Complete Date',
            'taxable' => 'Taxable',
            'driver' => 'Driver',
            'reference' => 'Reference',
            'loaded_miles' => 'Loaded<br/>Miles',
            'empty_miles' => 'Empty<br/>Miles',
            'origin_city' => 'Origin<br/>City/State',
            'destination_city' => 'Destination<br/>City/State',
            'quantity' => 'Qty',
            'rate' => 'Rate',
            'amount' => 'Amount',
        ];
    }

    public function getSettlementInfoFields(): array
    {
        $fieldNames = User::getCurrentUser()->getSelectedCarrier()->getCustomFieldNames();

        return [
            'id' => 'ID',
            'scarrier_name' => 'Division',
            'payment_code' => str_replace(' ', '<br/>', (string) $fieldNames->getPaymentCode()),
            'contractor_code' => 'Contractor<br/>Code',
            'powerunit_code' => 'Power Unit<br/>Code',
            'description' => $fieldNames->getDescription(),
            'category' => $fieldNames->getCategory(),
            'shipment' => 'Shipment',
            'shipment_complete_date' => 'Shipment<br/>Complete Date',
            'taxable' => 'Taxable',
            'driver' => 'Driver',
            'reference' => 'Reference',
            'loaded_miles' => 'Loaded<br/>Miles',
            'empty_miles' => 'Empty<br/>Miles',
            'origin_city' => 'Origin<br/>City/State',
            'destination_city' => 'Destination<br/>City/State',
            'quantity' => 'Qty',
            'rate' => 'Rate',
            'amount' => 'Amount',
        ];
    }

    /**
     * @param $cycleId
     * @param null $ids
     * @return $this
     */
    public function deleteCyclePayments($cycleId, $ids = null)
    {
        $criteria = ['settlement_cycle_id = ?' => $cycleId];
        if (is_array($ids)) {
            $criteria['id IN (?)'] = $ids;
        }

        $this->update(['deleted' => 1], $criteria);

        return $this;
    }

    /**
     * @param $cycleId int
     * @return $this
     */
    public function resetPayments($cycleId)
    {
        $this->update(['balance' => new Zend_Db_Expr('`amount`')], ['settlement_cycle_id = ?' => $cycleId]);

        return $this;
    }
}
