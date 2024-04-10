<?php

class Application_Model_Resource_Deductions_Deduction extends Application_Model_Base_Resource
{
    protected $_name = 'deductions';

    public function getInfoFields()
    {
        return [
            'id' => 'ID',
            'created_datetime' => 'Transaction<br/>Date',
            'powerunit_code' => 'PU Code',
            'deduction_code' => 'Code',
            'recurring_title' => 'Recurring',
            'reference' => 'Reference',
            'adjusted_balance' => 'Original<br/>Amount',
            'transaction_fee' => 'Transaction<br/>Fee',
            'amount' => 'Paid<br/>Amount',
            'balance' => 'Balance Remaining<br/>',
        ];
    }

    public function getSettlementInfoFields()
    {
        return [
            'id' => 'ID',
            'created_datetime' => 'Transaction<br/>Date',
            'powerunit_code' => 'PU Code',
            'deduction_code' => 'Code',
            'recurring_title' => 'Recurring',
            'reference' => 'Reference',
            'adjusted_balance' => 'Original<br/>Amount',
            'transaction_fee' => 'Transaction<br/>Fee',
            'amount' => 'Paid<br/>Amount',
            'balance' => 'Balance Remaining<br/>',
        ];
    }

    /**
     * @param $cycleId int
     * @return $this
     */
    public function resetDeductions($cycleId)
    {
        $this->update([
            'balance' => new Zend_Db_Expr('`adjusted_balance`'),
            'amount' => null,
        ], [
            'settlement_cycle_id = ?' => $cycleId,
        ]);

        return $this;
    }
}
