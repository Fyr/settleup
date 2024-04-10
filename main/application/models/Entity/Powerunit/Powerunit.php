<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Carrier as Division;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_System_ReserveTransactionTypes as ReserveTransactionTypes;

/**
 * @method Application_Model_Entity_Collection_Powerunit_Powerunit getCollection()
 * @method Application_Model_Resource_Powerunit_Powerunit getResource()
 */
class Application_Model_Entity_Powerunit_Powerunit extends Application_Model_Base_Entity
{
    public function setContractorDataByCode()
    {
        $contractor = new Contractor();
        $selectedContractorCode = $this->getContractorCode();
        $selectedContractor = $contractor->getContractorByCode(
            $selectedContractorCode
        );
        $this->setContractorId($selectedContractor['entity_id']);
        $this->setContractorName($selectedContractor['company_name']);
    }

    public function setDatetimesToDbFormat($isEdit = false)
    {
        if (!$this->getData('start_date') && !$isEdit) {
            // Let database create default value
            $this->unsetData('start_date');
        } else {
            $this->changeDatetimeFormat(['start_date']);
        }
        $this->changeDatetimeFormat(['termination_date']);
    }

    public function setDatetimesFromDbFormat()
    {
        $this->changeDatetimeFormat(
            [
                'start_date',
                'termination_date',
            ],
            true,
            true
        );
    }

    /**
     * @return Contractor
     */
    public function getContractor()
    {
        $contractorEntity = new Contractor();
        $contractorEntity->load($this->getContractorId(), 'entity_id');

        return $contractorEntity;
    }

    public function createIndividualTemplates(): void
    {
        (new Division())->createIndividualTemplatesByDivisionIds([$this->getCarrierId()]);
    }

    public function createIndividualTemplatesByDivisionIds(array $divisionIds): void
    {
        foreach ($divisionIds as $divisionId) {
            $divisionEntity = new Division();
            $divisionEntity->load($divisionId, 'entity_id');
            if ($divisionEntity->isEmpty()) {
                $this->getLogger()->alert('Failed create Individual Templates. Division not found by entity_id: ' .
                    $divisionId);
                continue;
            }
            $this->getLogger()->info('Create Individual Templates for DivisionId: ' . $divisionId);
            $divisionEntity->createPaymentTemplates();
            $divisionEntity->createDeductionTemplates();
        }
    }

    /**
     * Create reserve transactions for outstanding contributions from previous cycle
     *
     * @param $currentCycle Application_Model_Entity_Settlement_Cycle
     * @param $paymentSum float
     * @return array
     */
    public function processOutstandingContributions($currentCycle, $paymentSum): array
    {
        //pull a list of outstanding contributions
        $reserveTransactionModel = new Application_Model_Entity_Accounts_Reserve_Transaction();
        $reserveTransactionCollection = $reserveTransactionModel->getCollection()
            // add filter by Power Unit using JOIN as we don't have a column w/ Power Unit ID in Transaction table
//            ->addFieldsForSelect(
//                $reserveTransactionModel,
//                'reserve_account_contractor',
//                $this,
//                'id',
//                ['powerunit_id']
//            )
            ->addFilter('reserve_account.powerunit_id', $this->getId())
            ->addFilter('settlement_cycle_id', $currentCycle->getParentCycleId())
            ->addFilter('balance', 0, '>')
            ->addFilter('deleted', 0)
        ;

        $newTransactions = [];

        foreach ($reserveTransactionCollection->getItems() as $transaction) {

            // set initial balance = outstanding amount from previous cycle
            $initialAmount = $transaction->getBalance();

            $paidAmount = min($initialAmount, $paymentSum);
            $balance = $initialAmount - $paidAmount;

            $description =
                'Outstanding contribution of ' . $paidAmount . ' from Cycle #' . $currentCycle->getParentCycleId();

            $reserveTransactionModel = (new Application_Model_Entity_Accounts_Reserve_Transaction())
                // TODO replace in database, it's actually linked to Power Unit, but pending DB fields refactoring
                ->setReserveAccountContractor($transaction->getReserveAccountContractor())
                ->setPowerunitId($this->getId())
                ->setContractorId($transaction->getContractorId())
                ->setSettlementCycleId($currentCycle->getId())
                ->setParentTransactionId($transaction->getId())
                ->setDescription($description)
                ->setCreatedDatetime((new DateTime())->format('Y-m-d'))
                ->setType(ReserveTransactionTypes::CONTRIBUTION)
                ->setAmount($paidAmount)
                ->setBalance($balance)
                ->setAdjustedBalance($initialAmount)
                ->setCreatedBy(User::SYSTEM_USER);
            // decrease amount of available compensations
            $paymentSum -= $reserveTransactionModel->getAmount();
            $newTransactions[] = $reserveTransactionModel->save();
        }

        return $newTransactions;
    }

    /**
     * Create reserve transaction contribution to cover minimum balance
     *
     * @param $settlementCycle Application_Model_Entity_Settlement_Cycle
     * @param $paymentSum float
     * @return $this
     */
    public function updateReserveAccount($settlementCycle, $paymentSum)
    {
        $reserveAccountModel = new Application_Model_Entity_Accounts_Reserve_Powerunit();

        // get a list of all RA that has a balance < minimum
        $reserveAccounts = $reserveAccountModel->getCollection()
            ->addFilter(
                'powerunit_id',
                $this->getId()
            )
            // ->addActiveVendorFilter($this)
            ->addNonDeletedFilter()
            ->addFilter(
                'reserve_account.current_balance',
                'reserve_account.min_balance',
                '<',
                false
            )
            // ->setOrder('priority', 'ASC')
        ;

        // create reserve transaction contributions for given list of RA(s)
        foreach ($reserveAccounts->getItems() as $reserveAccount) {
            // calculate initial amounts
            $initialAmount = min(
                (float) $reserveAccount->getContributionAmount(),
                (float) $reserveAccount->getMinBalance() - (float) $reserveAccount->getCurrentBalance(),
            );
            $paidAmount = min($initialAmount, $paymentSum);
            $balance = $initialAmount - $paidAmount;

            $description = $paidAmount . ' contribution';

            $reserveTransactionModel = (new Application_Model_Entity_Accounts_Reserve_Transaction())
                ->setReserveAccountContractor($reserveAccount->getId())
                ->setPowerunitId($this->getId())
                ->setContractorId($this->getContractorId())
                ->setSettlementCycleId($settlementCycle->getId())
                ->setDescription($description)
                ->setCreatedDatetime((new DateTime())->format('Y-m-d'))
                ->setType(ReserveTransactionTypes::CONTRIBUTION)
                ->setAmount($paidAmount)
                ->setBalance($balance)
                ->setAdjustedBalance($initialAmount)
                ->setCreatedBy(User::SYSTEM_USER);
            // decrease amount of available compensations
            $paymentSum -= $reserveTransactionModel->getAmount();

            // save transaction if amount > 0
            if ($reserveTransactionModel->getAmount()) {
                $reserveTransactionModel->save();
            }

            // exit from a cycle if there is no money left
            if (!$paymentSum) {
                break;
            }
        }

        return $this;
    }
}
