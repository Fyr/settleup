<?php

class Application_Model_Resource_Deductions_Temp extends Application_Model_Base_Resource
{
    protected $_name = 'deductions_temp';

    public function getInfoFields(): array
    {
        return [
            'deduction_code' => 'Deduction Code',
            'provider_code' => 'Vendor Code',
            'contractor_code' => 'Contractor Code',
            'powerunit_code' => 'Power Unit Code',
            'description' => 'Description',
            'department' => 'Department',
            'reference' => 'Reference',
            'disbursement_date' => 'Disbursement Date',
            'invoice_date' => 'Transaction Date',
            'transaction_fee' => 'Transaction Fee',
            'amount' => 'Original Amount',
            'adjusted_balance' => 'Current Amount',
            'deduction_amount' => 'Deduction Amount',
            'balance' => 'Remaining Balance',
            'recurring_title' => 'Recurring',
            'title' => 'Status',
        ];
    }

    /**
     * @return Application_Model_Entity_Deductions_Deduction
     */
    public function getParentEntity()
    {
        return new Application_Model_Entity_Deductions_Deduction();
    }
}
