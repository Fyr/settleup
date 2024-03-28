<?php

use Application_Model_Entity_Accounts_Reserve as ReserveAccount;
use Application_Model_Entity_Accounts_Reserve_Contractor as ReserveAccountContractor;
use Application_Model_Entity_Accounts_Reserve_Transaction as Transaction;
use Application_Model_Entity_Entity_Contractor as Contractor;

/**
 * @method $this staticLoad($id, $field = null)
 * @method Application_Model_Resource_Accounts_Reserve_History getResource()
 */
class Application_Model_Entity_Accounts_Reserve_History extends Application_Model_Base_Entity
{
    public function getTransactionForReport()
    {
        $reserveAccountContractor = $this->getReserveAccountContractor();
        $contractor = Contractor::staticLoad($reserveAccountContractor->getEntityId(), 'entity_id');
        $vendorEntity = $reserveAccountContractor->getVendorAccount()->getReserveAccountEntity()->getEntity();
        $contractorVendor = Application_Model_Entity_Entity_ContractorVendor::staticLoad([
            'contractor_id' => $contractor->getEntityId(),
            'vendor_id' => $vendorEntity->getId(),
        ]);

        $transaction = new Transaction();
        $transaction->setCycleStartDate($this->getCycleStartDate());
        $transaction->setCycleCloseDate($this->getCycleCloseDate());
        $transaction->setStartingBalance($this->getStartingBalance());
        $transaction->setEndingBalance($this->getStartingBalance());
        $transaction->setReserveAccountContractor($this->getReserveAccountId());
        $transaction->setSettlementCycleId($this->getSettlementCycleId());
        $transaction->setAccountName($reserveAccountContractor->getAccountName());
        $transaction->setCompanyName($contractor->getName());
        $transaction->setDivision($contractor->getDivision());
        $transaction->setContractorCode($contractor->getCode());
        $transaction->setVendorReserveCode($reserveAccountContractor->getVendorReserveCode());
        $transaction->setVendorName($vendorEntity->getName());
        $transaction->setVendorAcct($contractorVendor->getVendorAcct());

        return $transaction;
    }

    /**
     * @return Application_Model_Entity_Accounts_Reserve
     */
    public function getReserveAccount()
    {
        $reserveAccount = new ReserveAccount();
        $reserveAccount->load($this->getReserveAccountId());

        return $reserveAccount;
    }

    /**
     * @return Application_Model_Entity_Accounts_Reserve_Contractor
     */
    public function getReserveAccountContractor()
    {
        $reserveAccount = new ReserveAccountContractor();
        $reserveAccount->load($this->getReserveAccountId(), 'reserve_account_id');

        return $reserveAccount;
    }
}
