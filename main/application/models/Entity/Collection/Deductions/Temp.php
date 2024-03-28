<?php

class Application_Model_Entity_Collection_Deductions_Temp extends Application_Model_Base_Collection
{
    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Application_Model_Entity_Deductions_Temp(),
            'status_id',
            new Application_Model_Entity_System_PaymentTempStatus(),
            'id',
            ['title']
        );

        return $this;
    }

    /**
     * return rate and quantity
     *
     * @return array
     */
    public function getTotals()
    {
        $data = [
            'amount' => 0,
            'balance' => 0,
            'adjusted_balance' => 0,
            'deduction_amount' => 0,
            'transaction_fee' => 0,
        ];

        /** @var Application_Model_Entity_Deductions_Temp $deduction */
        foreach ($this->getItems() as $deduction) {
            $data['amount'] += is_numeric($deduction->getAmount()) ? $deduction->getAmount() : 0;
            $data['balance'] += is_numeric($deduction->getBalance()) ? $deduction->getBalance() : 0;
            $data['adjusted_balance'] += is_numeric($deduction->getAdjustedBalance()) ? $deduction->getAdjustedBalance() : 0;
            $data['deduction_amount'] += is_numeric($deduction->getDeductionAmount()) ? $deduction->getDeductionAmount() : 0;
            $data['transaction_fee'] += is_numeric($deduction->getTransactionFee()) ? $deduction->getTransactionFee() : 0;
        }

        return $data;
    }
}
