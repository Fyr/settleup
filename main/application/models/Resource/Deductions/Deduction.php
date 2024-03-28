<?php

class Application_Model_Resource_Deductions_Deduction extends Application_Model_Base_Resource
{
    protected $_name = 'deductions';

    public function getInfoFields()
    {
        return [
            'id' => 'ID',
            'deduction_code' => 'Code',
            'contractor_code' => 'Contractor<br/>Code',
            'company_name' => 'Contractor',
            'powerunit_code' => 'Power Unit<br/>Code',
            'description' => 'Description',
            'recurring_title' => 'Recurring',
            'billing_title' => 'Frequency',
            'reference' => 'Reference',
            'invoice_date' => 'Transaction<br/>Date',
            'transaction_fee' => 'Transaction<br/>Fee',
            'amount' => 'Original<br/>Amount',
            'balance' => 'Remaining<br/>Balance',
            'adjusted_balance' => 'Current<br/>Amount',
            'deduction_amount' => 'Deduction<br/>Amount',
        ];
    }

    public function getSettlementInfoFields()
    {
        return [
            'id' => 'ID',
            'deduction_code' => 'Code',
            'contractor_code' => 'Contractor<br/>Code',
            'powerunit_code' => 'Power Unit<br/>Code',
            'description' => 'Description',
            'recurring_title' => 'Recurring',
            'billing_title' => 'Frequency',
            'reference' => 'Reference',
            'invoice_date' => 'Transaction<br/>Date',
            'transaction_fee' => 'Transaction<br/>Fee',
            'amount' => 'Original<br/>Amount',
            'balance' => 'Remaining<br/>Balance',
            'adjusted_balance' => 'Current<br/>Amount',
            'deduction_amount' => 'Deduction<br/>Amount',
        ];
    }

    /**
     * @param $cycleId int
     * @return $this
     */
    public function resetDeductions($cycleId)
    {
        $this->update([
            'balance' => new Zend_Db_Expr('`amount`'),
            //'adjusted_balance' => null,
        ], [
            'settlement_cycle_id = ?' => $cycleId,
        ]);

        return $this;
    }
}
