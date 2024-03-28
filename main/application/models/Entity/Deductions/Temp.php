<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Carrier as Division;
use Application_Model_Entity_Entity_Vendor as Vendor;
use Application_Model_Entity_System_ContractorStatus as ContractorStatus;
use Application_Model_Entity_System_RecurringTitle as RecurringTitle;
use Application_Model_Entity_System_SystemValues as SystemValues;
use Application_Model_Entity_System_VendorStatus as VendorStatus;

/**
 * @method Application_Model_Entity_Collection_Deductions_Temp getCollection()
 * @method Application_Model_Resource_Deductions_Temp getResource()
 */
class Application_Model_Entity_Deductions_Temp extends Application_Model_Base_TempEntity
{
    private $contractor;
    private $deductionSetup;
    private $vendor;
    private $provider;

    public function _beforeSave(): self
    {
        $this->fillFromTemplate();
        $this->check();
        $this->setCreatedBy(
            User::getCurrentUser()->getId()
        );
        $this->setCreatedDatetime(date('Y-m-d'));
        parent::_beforeSave();

        if ($this->getData('rate') > 10_000_000) {
            $this->setData('rate', 10_000_000);
        }

        if ($this->getData('rate') && $this->getData('rate') < -10_000_000) {
            $this->setData('rate', -10_000_000);
        }

        if ($this->getData('quantity') > 10_000_000) {
            $this->setData('quantity', 10_000_000);
        }

        if ($this->getData('quantity') && $this->getData('quantity') < -10_000_000) {
            $this->setData('quantity', -10_000_000);
        }

        return $this;
    }

    /**
     * get some field by code, check deduction status and add error
     * message on db
     *
     * @return Application_Model_Entity_Deductions_Temp
     */
    public function check(): self
    {
        return $this
            ->setupDeductionCode()
            ->setupVendorCode()
            ->setupContractorCode()
            ->setupPowerunitCode()
            ->setupOriginalAmount()
            ->setupCurrentAmount()
            ->setupBalance()
            ->setupDeductionAmount()
            ->setupTransactionFee()
            ->setupTransactionDate()
            ->setupDisbursementDate()
            ->setupRecurring()
            ->setupSetupId()
            ->setupStatusId();
    }

    private function setupDeductionCode(): self
    {
        if (!$this->getDeductionCode()) {
            $this->addJsonError(
                'Deduction Code is required and can not be empty (invalid Deduction Code)',
                $this->colDeductionCode()
            );
        }

        return $this;
    }

    private function setupContractorCode(): self
    {
        if (!$this->getContractorCode()) {
            $this->addJsonError(
                'Contractor Code is required and can not be empty (invalid Contractor Code)',
                $this->colContractorCode()
            );

            return $this;
        }

        $contractor = (new Application_Model_Entity_Entity_Contractor())
            ->load($this->getContractorCode(), 'code');
        if ($contractor->isEmpty()) {
            $this->addJsonError(
                'Contractor not found (invalid Contractor Code)',
                $this->colContractorCode()
            );

            return $this;
        }

        if ($contractor->getStatus() != ContractorStatus::STATUS_ACTIVE) {
            $this->addJsonError('Contractor has no active status', $this->colContractorCode());
        }

        if ($contractor->getSettlementGroupId() !== $this->getCurrentSettlementGroupId()) {
            $this->addJsonError('Powerunit Contractor\'s Settlement Group does not match currently selected Settlement Group (invalid Contractor Code)', $this->colContractorCode());
        }

        $provider = $this->getProvider();
        if ($provider instanceof Vendor) {
            $contractorVendor = (new Application_Model_Entity_Entity_ContractorVendor())
                ->getCollection()
                ->addFilter('contractor_id', $contractor->getEntityId())
                ->addFilter('vendor_id', $this->getProviderId())
                ->getFirstItem();
            if ($contractorVendor->isEmpty()) {
                $this->addJsonError(
                    'Contractor ' . $this->getContractorCode() . ' is not associated with Vendor ' . $this->getProviderCode(),
                    $this->colContractorCode()
                );
            }
            if (VendorStatus::STATUS_ACTIVE !== $contractorVendor->getStatus()) {
                $this->addJsonError(
                    'Contractors\'s Vendor ' . $this->getProviderCode() . ' has not approved status',
                    $this->colContractorCode()
                );
            }
        }
        if ($provider instanceof Division) {
            if ((int) $contractor->getCarrierId() !== (int) $this->getProviderId()) {
                $this->addJsonError(
                    'Contractor ' . $this->getContractorCode() . ' is not associated with Division ' . $this->getProviderCode(),
                    $this->colContractorCode()
                );
            }
        }

        $this->setContractorId($contractor->getEntityId());

        return $this;
    }

    private function setupPowerunitCode(): self
    {
        if (!$this->getPowerunitCode()) {
            $this->addJsonError(
                'Power Unit is required and can not be empty (invalid Power Unit)',
                $this->colPowerunitCode()
            );

            return $this;
        }

        $powerunit = (new Application_Model_Entity_Powerunit_Powerunit())
            ->load($this->getPowerunitCode(), 'code');
        if ($powerunit->isEmpty()) {
            $this->addJsonError(
                'Power Unit not found (invalid Power Unit)',
                $this->colPowerunitCode()
            );

            return $this;
        }

        if ((int) $powerunit->getContractorId() !== (int) $this->getContractorId()) {
            $this->addJsonError(
                'Power Unit ' . $powerunit->getCode() . ' is not associated with Contractor ' . $this->getContractorCode(),
                $this->colPowerunitCode()
            );
        }

        $currentCarrier = User::getCurrentUser()->getSelectedCarrier();
        if ((int) $currentCarrier->getEntityId() !== (int) $powerunit->getCarrierId()) {
            $this->addWarning('Division is different from the current one');
        }

        $this->setPowerunitId($powerunit->getId());

        return $this;
    }

    private function setupVendorCode(): self
    {
        if (!$this->getProviderCode()) {
            $this->addJsonError(
                'Vendor Code is required and can not be empty (invalid Contractor Code)',
                $this->colProviderCode()
            );

            return $this;
        }

        $provider = $this->getProvider();
        if ($provider->isEmpty()) {
            $this->addJsonError('Division/Vendor not found (invalid Vendor Code)', $this->colProviderCode());

            return $this;
        }

        $this->setProviderId($provider->getEntityId());

        return $this;
    }

    private function setupOriginalAmount(): self
    {
        if (!$this->getAmount()) {
            $this->addJsonError(
                'Original Amount is required and can not be empty (invalid Original Amount)',
                $this->colAmount()
            );

            return $this;
        }

        if (!is_numeric($this->getAmount())) {
            $this->addJsonError(
                'Original Amount is invalid (Should be numeric)',
                $this->colAmount()
            );
        }

        return $this;
    }

    private function setupCurrentAmount(): self
    {
        if (!$this->getAdjustedBalance() && $this->getAmount() && '0' != $this->getAdjustedBalance()) {
            $this->setAdjustedBalance($this->getAmount());
        }

        if ($this->getAdjustedBalance() && !is_numeric($this->getAdjustedBalance())) {
            $this->addJsonError(
                'Current Amount is invalid (Should be numeric)',
                $this->colAdjustedBalance()
            );
        }

        return $this;
    }

    private function setupBalance(): self
    {
        if ($this->getBalance()) {
            if (!is_numeric($this->getBalance())) {
                $this->addJsonError(
                    'Remaining Balance is invalid (Should be numeric)',
                    $this->colBalance()
                );
            }
        }

        return $this;
    }

    private function setupDeductionAmount(): self
    {
        if ($this->getDeductionAmount()) {
            if (!is_numeric($this->getDeductionAmount())) {
                $this->addJsonError(
                    'Deduction Amount is invalid (Should be numeric)',
                    $this->colDeductionAmount()
                );
            }
        }

        return $this;
    }

    private function setupTransactionFee(): self
    {
        if ($this->getTransactionFee()) {
            if (!is_numeric($this->getTransactionFee())) {
                $this->addJsonError(
                    'Transaction Fee is invalid (Should be numeric)',
                    $this->colTransactionFee()
                );
            }
        }

        return $this;
    }

    private function setupSetupId(): self
    {
        $deductionSetup = $this->getDeductionSetup();
        if (!$deductionSetup->isEmpty()) {
            $this->setSetupId($deductionSetup->getId());
            $this->setRate($deductionSetup->getRate());
            $this->setQuantity($deductionSetup->getQuantity());
        }

        return $this;
    }

    private function setupTransactionDate(): self
    {
        $this->checkDate($this->getInvoiceDate(), $this->colInvoiceDate(), true);

        return $this;
    }

    private function setupDisbursementDate(): self
    {
        $this->checkDate($this->getDisbursementDate(), $this->colDisbursementDate(), true);

        return $this;
    }

    private function setupRecurring(): self
    {
        if (!$this->getRecurring()) {
            $this->setRecurring(RecurringTitle::RECURRING_NO);

            return $this;
        }

        $this->setRecurringTitle($this->getRecurring());

        match (mb_strtolower((string) $this->getRecurring())) {
            '1', 'yes' => $this->setRecurring(RecurringTitle::RECURRING_YES),
            '0', 'no' => $this->setRecurring(RecurringTitle::RECURRING_NO),
            default => $this->addJsonError(
                'Invalid Recurring (acceptable: Yes/No)',
                $this->colRecurring()
            ),
        };

        if (RecurringTitle::RECURRING_YES === (int) $this->getRecurring()) {
            $cycle = User::getCurrentUser()->getCurrentCycle();
            if (!$cycle->isEmpty()) {
                $this->setBillingCycleId($cycle->getCyclePeriodId());
                $this->setFirstStartDay($cycle->getFirstStartDay());
                $this->setSecondStartDay($cycle->getSecondStartDay());
            }
        }

        return $this;
    }

    /**
     * @return Application_Model_Entity_Deductions_Setup|null
     */
    public function getDeductionSetup()
    {
        if (!isset($this->deductionSetup)) {
            $deductionSetupEntity = new Application_Model_Entity_Deductions_Setup();
            if ($this->getDeductionCode() && ($providerId = $this->getProvider()->getEntityId())) {
                $collection = $deductionSetupEntity->getCollection();
                $collection->addFilter(
                    'deduction_code',
                    $this->getDeductionCode()
                );
                $collection->addFilter(
                    'provider_id',
                    $providerId
                );
                $collection->addUserVisibilityFilter();
                $collection->addNonDeletedFilter();
                $deductionSetupEntity = $collection->getFirstItem();
                if ($deductionSetupEntity->getId() && $this->getPowerunitId()) {
                    $individualSetup = Application_Model_Entity_Deductions_Setup::staticLoad([
                        'master_setup_id' => $deductionSetupEntity->getId(),
                        'powerunit_id' => $this->getPowerunitId(),
                        'deleted' => 0,
                    ]);
                    $deductionSetupEntity = $individualSetup;
                }
            }

            $this->deductionSetup = $deductionSetupEntity;
        }

        return $this->deductionSetup;
    }

    /**
     * @return Application_Model_Entity_Entity_Contractor|null
     */
    public function getContractor()
    {
        if (!isset($this->contractor)) {
            $carrier = User::getCurrentUser()->getSelectedCarrier();
            $entity = new Application_Model_Entity_Entity_Contractor();
            $collection = $entity->getCollection();
            $collection->addFilter(
                'carrier_id',
                $carrier->getEntityId()
            );
            $collection->addFilter(
                'code',
                $this->getContractorCode()
            );
            $this->contractor = $collection->getFirstItem();
        }

        return $this->contractor;
    }

    public function getVendor()
    {
        if (!isset($this->vendor)) {
            if ($this->getProviderId()) {
                $this->vendor = Application_Model_Entity_Entity::staticLoad(
                    $this->getProviderId(),
                    'id'
                )->getEntityByType();
            } elseif (!$this->getVendorCode()) {
                $this->vendor = new Vendor();
            } else {
                $user = User::getCurrentUser();
                $carrier = $user->getSelectedCarrier();
                if ($user->isVendor() && $this->getVendorCode()) {
                }
                if ($this->getVendorCode() == $carrier->getId() && !$user->isVendor()) {
                    if ($carrier->getStatus() == SystemValues::CONFIGURED_STATUS) {
                        $this->vendor = $carrier;
                    }
                } else {
                    $entity = new Vendor();
                    if ($user->isVendor() && $this->getVendorCode() != $user->getEntity()->getCode()) {
                        $this->vendor = $entity;
                    } else {
                        $collection = $entity->getCollection();
                        $collection->addFilter(
                            'carrier_id',
                            $carrier->getEntityId()
                        );
                        $collection->addFilter(
                            'code',
                            $this->getVendorCode()
                        );
                        $collection->addConfiguredFilter();
                        $this->vendor = $collection->getFirstItem();
                    }
                }
            }
        }

        return $this->vendor;
    }

    public function getProvider()
    {
        if (!isset($this->provider)) {
            $entity = new Vendor();
            $entity->load($this->getProviderCode(), 'code');
            if ($entity->isEmpty()) {
                $entity = new Division();
                $entity->load($this->getProviderCode(), 'short_code');
                if (!$entity->isEmpty()) {
                    $this->setProviderCode($entity->getShortCode());
                }
            } else {
                $this->setProviderCode($entity->getCode());
            }
            $this->provider = $entity;
        }

        return $this->provider;
    }

    private function getCurrentSettlementGroupId()
    {
        if ($currentSettlementGroupIdFromCache = Application_Model_Cache::load('currentSettlementGroupId')) {
            $this->getLogger()->info('Deduction Temp. Get CurrentSettlementGroupId from cache');

            return $currentSettlementGroupIdFromCache;
        }
        $user = User::getCurrentUser();
        $currentSettlementGroupId = $user->getLastSelectedSettlementGroup();
        Application_Model_Cache::save('currentSettlementGroupId', $currentSettlementGroupId);
        $this->getLogger()->info('Deduction Temp. Get CurrentSettlementGroupId from db');

        return $currentSettlementGroupId;
    }

    /**
     * @return string
     */
    public function getControllerName()
    {
        return 'deductions_deductions';
    }

    public function fillFromTemplate()
    {
        if ($deductionCode = $this->getDeductionCode()) {
            $template = $this->getDeductionSetup();
            if ($template->getId()) {
                $newTemplate = clone($template);
                $newTemplate->unsRate()->unsInvoiceDate()->unsInvoice()->unsContractorId()->unsId()->unsProviderId();
                $this->appendData($newTemplate->getData(), true);
            }
        }
        if (!$this->getQuantity()) {
            $this->setQuantity(1);
        }
        if (!$this->getRecurring()) {
            $this->setRecurring(0);
        }

        return $this;
    }

    public function getCycle()
    {
        $cycle = $this->getData('cycle');
        if (!$cycle) {
            $cycle = new Application_Model_Entity_Settlement_Cycle();
            if ($cycleId = $this->getSettlementCycleId()) {
                $cycle->load($cycleId);
            }
            $this->setCycle($cycle);
        }

        return $cycle;
    }

    public function getExportCollection($idOrFilters = null)
    {
        $entity = new Application_Model_Entity_Deductions_Deduction();
        if ((int)$idOrFilters && !is_array($idOrFilters)) {
            $collection = [$entity->load($idOrFilters)];
        } else {
            $collection = $entity->getCollection()->addCarrierFilter()->addContractorFilter()->addNonDeletedFilter(
            )->addSettlementFilter();
            $this->applyFilters($collection, $idOrFilters);
        }

        return $collection;
    }
}
