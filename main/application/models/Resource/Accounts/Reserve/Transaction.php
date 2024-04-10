<?php

use Application_Model_Entity_System_ReserveTransactionTypes as ReserveTransactionTypes;

class Application_Model_Resource_Accounts_Reserve_Transaction extends Application_Model_Base_Resource
{
    protected $_name = 'reserve_transaction';

    public function getInfoFields()
    {
        return [
            'id' => 'Transaction ID',
            'contractor_code' => 'Contractor Code',
            'powerunit_code' => 'PU Code',
            'ra_code' => 'RA Code',
            'ra_name' => 'RA Name',
            'type' => 'Transaction Type',
            'amount' => 'Paid Amount',
            'adjusted_balance' => 'Initial Amount',
            'balance' => 'Balance',
            'created_datetime' => 'Transaction Date',
        ];
    }

    public function getInfoFieldsForSettlementGrid()
    {
        return [
            'created_datetime' => 'Transaction Date',
            'description' => 'Description',
            'reference' => 'Reference',
            'title' => 'Type',
            'powerunit_code' => 'Power Unit',
            'balance' => 'Remaining Balance',
            'amount' => 'Amount',
        ];
    }

    /**
     * @param $cycleId int
     * @return $this
     */
    public function deleteCycleTransactions($cycleId)
    {
        $this->update(['deleted' => 1], ['settlement_cycle_id = ?' => $cycleId]);

        return $this;
    }

    public function deleteWithdrawals($cycleId)
    {
        $this->update(['deleted' => 1], [
            'settlement_cycle_id = ?' => $cycleId,
            'type = ?' => ReserveTransactionTypes::WITHDRAWAL,
        ]);

        return $this;
    }

    public function deleteContributions($cycleId)
    {
        $this->update(['deleted' => 1], [
            'settlement_cycle_id = ?' => $cycleId,
            'type = ?' => ReserveTransactionTypes::CONTRIBUTION,
        ]);

        return $this;
    }

    public function updateReserveAccountContractorCurrentBalance($reserveAccountContractorId, $settlementCycleId)
    {
        $sql = 'CALL updateReserveAccountContractorCurrentBalance(?,?)';
        $stmt = $this->getAdapter()->prepare($sql);
        $stmt->bindParam(1, $reserveAccountContractorId);
        $stmt->bindParam(2, $settlementCycleId);
        $stmt->execute();

        return $this;
    }

    public function updateReserveAccountContractorStartingBalance($reserveAccountContractorId, $settlementCycleId)
    {
        $sql = 'CALL updateReserveAccountContractorStartingBalance(?,?)';
        $stmt = $this->getAdapter()->prepare($sql);
        $stmt->bindParam(1, $reserveAccountContractorId);
        $stmt->bindParam(2, $settlementCycleId);
        $stmt->execute();

        $this->updateReserveAccountContractorCurrentBalance($reserveAccountContractorId, $settlementCycleId);

        return $this;
    }
}
