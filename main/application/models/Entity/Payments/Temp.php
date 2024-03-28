<?php

use Application_Model_Entity_Accounts_User as AccountsUser;
use Application_Model_Entity_System_ContractorStatus as ContractorStatus;

class Application_Model_Entity_Payments_Temp extends Application_Model_Base_TempEntity
{
    public $paymentSetup;
    public $contractor;

    /**
     * get some field by code, check payment status and add error message on db
     *
     * @return Application_Model_Entity_Payments_Temp
     */
    public function check()
    {
        //        if ($this->getId()) {
        //            $cycle = $this->getCycle();
        //            if ($cycle->getId()) {
        //                $closeDate = new Zend_Date($cycle->getCycleCloseDate(), Zend_Date::ISO_8601);
        //                $invoiceDate = new Zend_Date($this->getInvoiceDate(), Zend_Date::ISO_8601);
        //                if ($closeDate->isEarlier($invoiceDate, Zend_Date::DATES)) {
        //                    $error .= 'Invoice Date is not older or within selected settlement cycles<br>';
        //                }
        //            }
        //        }

        if (!$this->getContractor()->getId()) {
            $this->addError('Contractor not found (invalid Contactor Code)');
        } else {
            $this->setContractorId($this->getContractor()->getEntityId());
        }

        if ($this->getContractor()->getStatus() == ContractorStatus::STATUS_NOT_CONFIGURED) {
            $this->addError('Contractor is not configured');
        }
        if ($this->getContractor()->getStatus() == ContractorStatus::STATUS_TERMINATED) {
            $this->addWarning('Contractor is terminated');
        }

        $user = AccountsUser::getCurrentUser();
        $currentSettlementGroupId = $user->getLastSelectedSettlementGroup();
        if ($this->getContractor()->getSettlementGroupId() !== $currentSettlementGroupId) {
            $this->addError('Contractor\'s Settlement Group does not match currently selected Settlement Group (invalid Contractor Code)');
        }

        if (!$this->getInvoiceDate() || !Zend_Date::isDate($this->getInvoiceDate(), 'Y-m-d')) {
            $this->addError('Invoice Date is not valid');
        }

        if ($this->getDisbursementDate()) {
            if (!$this->checkIsDate($this->colDisbursementDate(), true)) {
                $this->addError('Date in field ' . $this->colDisbursementDate() . ' is invalid (acceptable: '
                    . self::DATE_FORMAT . ')');
            }
            if ('Friday' !== date('l', strtotime((string) $this->getDisbursementDate()))) {
                $this->addError('Disbursement Date is not valid, the day of the week must be Friday');
            }
        }

        if (!preg_match('/^(-?\d+\.?\d*)$/', (string) $this->getRate()) || !(fmod(
            $this->getRate(),
            0.01
        ) < 0.000001 || 0.01 - fmod($this->getRate(), 0.01) < 0.000001)) {
            $this->addError('Rate is not validd');
        }

        if ($this->getTerms()) {
            if (filter_var($this->getTerms(), FILTER_VALIDATE_INT) === false) {
                $this->addError('Terms is not valid');
            }
        }

        if ($this->getQuantity()) {
            if (
                !filter_var($this->getQuantity(), FILTER_VALIDATE_INT)
                || $this->getQuantity() > 1_000_000
                || $this->getQuantity() < -1_000_000
            ) {
                $this->addError('Quantity is not valid');
            }
        }

        if ($this->getRate()) {
            if ($this->getRate() > 1_000_000 || $this->getRate() < -1_000_000) {
                $this->addError('Rate is not valid');
            }
        }

        $this->setSetupId($this->getPaymentSetup()->getId());
        if (!$this->getSetupId()) {
            $this->addWarning('Template not found!');
        }

        return $this
            ->setupPowerunitCode()
            ->setupStatusId();
    }

    /**
     * @return Application_Model_Base_Entity
     * |Application_Model_Entity_Payments_Temp
     */
    public function _beforeSave()
    {
        $this->fillFromTemplate();
        $this->check();
        $this->setCreatedBy(AccountsUser::getCurrentUser()->getId());
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
     * return contractor for carrier's my_contractor by contractor_code
     *
     * @return Application_Model_Entity_Entity_Contractor|null
     */
    public function getContractor()
    {
        if (!isset($this->contractor)) {
            $carrier = AccountsUser::getCurrentUser()->getSelectedCarrier();
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

    /**
     * @return Application_Model_Entity_Payments_Setup|null
     */
    public function getPaymentSetup()
    {
        if (!isset($this->paymentSetup)) {
            $carrier = AccountsUser::getCurrentUser()->getSelectedCarrier();
            $paymentSetupEntity = new Application_Model_Entity_Payments_Setup();
            if ($this->getPaymentCode()) {
                $collection = $paymentSetupEntity->getCollection();
                $collection->addFilter(
                    'payment_setup.payment_code',
                    $this->getPaymentCode()
                );
                $collection->addFilter(
                    'payment_setup.carrier_id',
                    $carrier->getEntityId()
                );
                $collection->addNonDeletedFilter();
                $paymentSetupEntity = $collection->getFirstItem();
                if ($paymentSetupEntity->getId() && $this->getContractor()->getId() && $paymentSetupEntity->getId()) {
                    $individualSetup = Application_Model_Entity_Payments_Setup::staticLoad([
                        'master_setup_id' => $paymentSetupEntity->getId(),
                        'contractor_id' => $this->getContractor()->getEntityId(),
                        'deleted' => 0,
                    ]);
                    $paymentSetupEntity = $individualSetup;
                }
            }
            $this->paymentSetup = $paymentSetupEntity;
        }

        return $this->paymentSetup;
    }

    private function setupPowerunitCode(): self
    {
        if (!$this->getPowerunitCode()) {
            $this->addError('Power Unit is required and can not be empty (invalid Power Unit)');

            return $this;
        }

        $powerunit = new Application_Model_Entity_Powerunit_Powerunit();
        $powerunit->load($this->getPowerunitCode(), 'code');

        if ($powerunit->isEmpty()) {
            $this->addError('Power Unit with Contractor not found (invalid Power Unit)');

            return $this;
        }

        if ((int) $powerunit->getContractorId() !== (int) $this->getContractorId()) {
            $this->addError('Power Unit ' . $this->getPowerunitCode() . ' is not associated with Contractor '
                . $this->getContractorCode());

            return $this;
        }

        $this->setPowerunitId($powerunit->getId());

        return $this;
    }

    public function getControllerName()
    {
        return 'payments_payments';
    }

    public function fillFromTemplate()
    {
        if ($paymentCode = $this->getPaymentCode()) {
            $template = $this->getPaymentSetup();
            if ($template->getId()) {
                //                $newData = array_merge($template->getData(), $this->getData());
                //                $this->setData($newData);
                $newTemplate = clone($template);
                $newTemplate->unsRate()->unsInvoiceDate()->unsInvoice()->unsContractorId()->unsId();
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
        $entity = new Application_Model_Entity_Payments_Payment();
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
