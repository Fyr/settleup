<?php

class Application_Model_Entity_Collection_Payments_Temp extends Application_Model_Base_Collection
{
    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Application_Model_Entity_Payments_Temp(),
            'status_id',
            new Application_Model_Entity_System_PaymentTempStatus(),
            'id',
            ['title']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Payments_Temp(),
            'powerunit_id',
            new Application_Model_Entity_Powerunit_Powerunit(),
            'id',
            ['powerunit_code' => 'code']
        );

        $this->addFieldsForSelect(
            new Application_Model_Entity_Powerunit_Powerunit(),
            'contractor_id',
            new Application_Model_Entity_Entity_Contractor(),
            'entity_id',
            ['contractor_code' => 'code', 'company_name']
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
            'rate' => 0,
            'quantity' => 0,
        ];

        /** @var Application_Model_Entity_Payments_Temp $payment */
        foreach ($this->getItems() as $payment) {
            $data['rate'] += $payment->getRate();
            $data['quantity'] += $payment->getQuantity();
        }

        return $data;
    }
}
