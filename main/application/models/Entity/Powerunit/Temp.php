<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Powerunit_Powerunit as Powerunit;
use Application_Model_Entity_Powerunit_Temp as PowerunitTemp;
use Application_Model_Entity_System_ContractorStatus as ContractorStatus;
use Application_Model_Entity_System_DivisionTitle as DivisionTitle;
use Application_Model_Entity_System_PowerunitOwnerType as PowerunitOwnerType;
use Application_Model_Entity_System_PowerunitStatus as PowerunitStatus;
use Application_Model_Entity_System_SystemValues as SystemValues;

/**
 * @method Application_Model_Entity_Collection_Powerunit_Temp getCollection()
 * @method Application_Model_Resource_Powerunit_Temp getResource()
 */
class Application_Model_Entity_Powerunit_Temp extends Application_Model_Base_TempEntity
{
    final public const YEAR_LENGTH = 4;

    public function _beforeSave(): self
    {
        $this->check();
        parent::_beforeSave();

        return $this;
    }

    public function check(): self
    {
        return $this
            ->setupDivision()
            ->setupPowerunitCode()
            ->setupContractorCode()
            ->setupStartDate()
            ->setupTerminationDate()
            ->setupStatus()
            ->setupPlateOwner()
            ->setupForm2290()
            ->setupIftaFilingOwner()
            ->setupVin()
            ->setupTractorYear()
            ->setupLicense()
            ->setupLicenseState()
            ->setupStatusId();
    }

    private function setupDivision(): self
    {
        $currentDivisionId = $this->getCurrentDivisionId();
        $this->setCarrierId($currentDivisionId);
        if (!$this->getDivisionCode()) {
            $this->addJsonError(
                'Division is required and can not be empty (invalid Division)',
                $this->colDivisionCode()
            );

            return $this;
        }

        $divisions = $this->getDivisions();
        $divisionId = $divisions[$this->getDivisionCode()] ?? null;
        if (!$divisionId) {
            $this->addJsonError(
                'Division code not found (invalid Division code)',
                $this->colDivisionCode()
            );

            return $this;
        }

        $acceptableDivisions = (new DivisionTitle())->getALl();
        $isTestDivision = $this->isTestValue($this->getDivisionCode());
        if (!in_array($this->getDivisionCode(), $acceptableDivisions) && !$isTestDivision) {
            $this->addJsonError(
                'Division is invalid (acceptable: ' . implode(', ', $acceptableDivisions) . ')',
                $this->colDivisionCode()
            );

            return $this;
        }

        $this->setCarrierId($divisionId);
        if ($currentDivisionId !== (int) $this->getCarrierId()) {
            $this->addWarning('Division is different from the current one');
        }

        return $this;
    }

    private function setupPowerunitCode(): self
    {
        if (!$this->getCode()) {
            $this->addJsonError('Code is required and can not be empty (invalid Code)', $this->colCode());

            return $this;
        }

        if (in_array($this->getCode(), $this->getExistCodes())) {
            $this->addJsonError('Code already in use (invalid Code)', $this->colCode());
        }
        $existTempCodes = $this->getExistTempCodes();
        if (in_array($this->getCode(), $existTempCodes)) {
            $this->addJsonError('Duplicate Code in uploaded data (invalid Code)', $this->colCode());
        }
        $existTempCodes[] = $this->getCode();
        Application_Model_Cache::save('existTempCodes', $existTempCodes);

        return $this;
    }

    private function setupStartDate(): self
    {
        $this->checkDate($this->getStartDate(), $this->colStartDate());

        return $this;
    }

    private function setupTerminationDate(): self
    {
        $this->checkDate($this->getTerminationDate(), $this->colTerminationDate());

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

        $contractorCodes = $this->getContractorCodes();
        $contractorStatus = $contractorCodes[$this->getContractorCode()] ?? null;
        if (!$contractorStatus) {
            $this->addJsonError('Contractor Code not found (invalid Contactor Code)', $this->colContractorCode());
        }
        if ($contractorStatus && ContractorStatus::STATUS_ACTIVE !== $contractorStatus) {
            $this->addWarning('Contractor is not active');
        }

        return $this;
    }

    private function setupStatus(): self
    {
        match (mb_strtolower((string) $this->getStatus())) {
            'active' => $this->setStatus(PowerunitStatus::STATUS_ACTIVE),
            'inactive' => $this->setStatus(PowerunitStatus::STATUS_INACTIVE),
            default => $this->setStatus(PowerunitStatus::STATUS_UNAVAILABLE),
        };

        return $this;
    }

    private function setupForm2290(): self
    {
        if (!$this->getForm2290()) {
            $this->addJsonError('2290 is required and can not be empty (invalid 2290)', $this->colForm2290());

            return $this;
        }

        match (mb_strtolower((string) $this->getForm2290())) {
            'yes' => $this->setForm2290(1),
            'no' => $this->setForm2290(0),
            default => $this->addJsonError('2290 is invalid (acceptable: Yes/No)', $this->colForm2290()),
        };

        return $this;
    }

    private function setupPlateOwner(): self
    {
        if (!$this->getPlateOwner()) {
            $this->addJsonError(
                'Plate Owner is required and can not be empty (invalid Plate Owner)',
                $this->colPlateOwner()
            );

            return $this;
        }

        match (mb_strtolower((string) $this->getPlateOwner())) {
            'fa' => $this->setPlateOwner(PowerunitOwnerType::OWNER_TYPE_FA),
            'contractor' => $this->setPlateOwner(PowerunitOwnerType::OWNER_TYPE_CONTRACTOR),
            default => $this->addJsonError(
                'Plate Owner is invalid (acceptable: FA/Contractor)',
                $this->colPlateOwner()
            ),
        };

        return $this;
    }

    private function setupIftaFilingOwner(): self
    {
        if (!$this->getIftaFilingOwner()) {
            $this->addJsonError(
                'Ifta Filing Owner is required and can not be empty (invalid Ifta Filing Owner)',
                $this->colIftaFilingOwner()
            );

            return $this;
        }

        match (mb_strtolower((string) $this->getData()["ifta_filing_owner"])) {
            'fa' => $this->setIftaFilingOwner(PowerunitOwnerType::OWNER_TYPE_FA),
            'contractor' => $this->setIftaFilingOwner(PowerunitOwnerType::OWNER_TYPE_CONTRACTOR),
            default => $this->addJsonError(
                'Ifta Filing Owner is invalid (acceptable: FA/Contractor)',
                $this->colIftaFilingOwner()
            ),
        };

        return $this;
    }

    private function setupVin(): self
    {
        if (!$this->getVin()) {
            $this->addJsonError('Vin is required and can not be empty (invalid Vin)', $this->colVin());
        }

        return $this;
    }

    private function setupTractorYear(): self
    {
        if (!$this->getTractorYear()) {
            $this->addJsonError(
                'Tractor Year is required and can not be empty (invalid Tractor Year)',
                $this->colTractorYear()
            );

            return $this;
        }

        if ($this->getTractorYear()) {
            if (self::YEAR_LENGTH !== strlen((string) $this->getTractorYear())
                || !filter_var($this->getTractorYear(), FILTER_VALIDATE_INT)) {
                $this->addJsonError(
                    'Tractor Year is invalid (Should be ' . self::YEAR_LENGTH . ' digit)',
                    $this->colTractorYear()
                );
            }
        }

        return $this;
    }

    private function setupLicense(): self
    {
        if (!$this->getLicense()) {
            $this->addJsonError(
                'License is required and can not be empty (invalid License)',
                $this->colLicense()
            );
        }

        return $this;
    }

    private function setupLicenseState(): self
    {
        if (!$this->getLicenseState()) {
            $this->addJsonError(
                'License State is required and can not be empty (invalid License State)',
                $this->colLicenseState()
            );

            return $this;
        }

        if ($this->getLicenseState() && !in_array($this->getLicenseState(), SystemValues::getStates())) {
            $this->addJsonError('License State is invalid (Example: CA, NY, etc.)', $this->colLicenseState());
        }

        return $this;
    }

    private function getDivisions(): array
    {
        if ($divisionsFromCache = Application_Model_Cache::load('divisions')) {
            $this->getLogger()->info('Power Unit Temp. Get Divisions from cache');

            return $divisionsFromCache;
        }

        $items = (new Application_Model_Entity_Entity_Carrier())
            ->getCollection()
            ->addNonDeletedFilter()
            ->getItems();

        $divisions = [];
        foreach ($items as $item) {
            $divisions[$item->getName()] = $item->getEntityId();
        }
        Application_Model_Cache::save('divisions', $divisions);
        $this->getLogger()->info('Power Unit Temp. Get Divisions from db');

        return $divisions;
    }

    private function getCurrentDivisionId(): ?int
    {
        if ($currentDivisionIdFromCache = Application_Model_Cache::load('currentDivisionId')) {
            return $currentDivisionIdFromCache;
        }
        $user = User::getCurrentUser();
        $currentDivisionId = $user->getEntity()->getCurrentCarrier()->getEntityId();
        Application_Model_Cache::save('currentDivisionId', $currentDivisionId);

        return $currentDivisionId;
    }

    private function getExistCodes(): array
    {
        $cacheKey = 'existCodes' . $this->getCarrierId();
        if ($existCodesFromCache = Application_Model_Cache::load($cacheKey)) {
            return $existCodesFromCache;
        }
        $powerunitCodes = (new Powerunit())
            ->getCollection()
            ->addNonDeletedFilter()
            ->addFilter('carrier_id', $this->getCarrierId())
            ->getField('code');

        Application_Model_Cache::save($cacheKey, $powerunitCodes);

        return $powerunitCodes;
    }

    private function getExistTempCodes(): array
    {
        return Application_Model_Cache::load('existTempCodes') ?: [];
    }

    private function getContractorCodes(): array
    {
        $cacheKey = 'contractorCodes' . $this->getCarrierId();
        if ($contractorCodesFromCache = Application_Model_Cache::load($cacheKey)) {
            return $contractorCodesFromCache;
        }

        $items = (new Contractor())
            ->getCollection()
            ->addNonDeletedFilter()
            ->addFilter('carrier_id', $this->getCarrierId())
            ->getItems();

        $contractorCodes = [];
        foreach ($items as $item) {
            $contractorCodes[$item->getCode()] = $item->getStatus();
        }
        Application_Model_Cache::save($cacheKey, $contractorCodes);

        return $contractorCodes;
    }

    public function getControllerName(): string
    {
        return 'powerunits_index';
    }

    public function getExportCollection($idOrFilters = null)
    {
        if ($idOrFilters['isTemp'] ?? false) {
            $entity = new PowerunitTemp();
            unset($idOrFilters['isTemp']);
            $collection = $entity
                ->getCollection()
                ->addTempStatusInfo()
                ->setOrder('id', Application_Model_Base_Collection::SORT_ORDER_ASC);
            $this->applyFilters($collection, $idOrFilters);

            return $collection;
        }

        $entity = new Powerunit();
        if ((int)$idOrFilters && !is_array($idOrFilters)) {
            $collection = [$entity->load($idOrFilters)];
        } else {
            $collection = $entity->getCollection()->addCarrierFilter()->addContractorFilter()->addNonDeletedFilter();
            $this->applyFilters($collection, $idOrFilters);
        }

        return $collection;
    }
}
