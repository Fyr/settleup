<?php

use Application_Model_Base_CryptAdvanced as Crypt;
use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Contact_Type as ContactType;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Entity_ContractorTemp as ContractorTemp;
use Application_Model_Entity_System_BookkeepingType as BookkeepingType;
use Application_Model_Entity_System_ContractorPersonType as ContractorPersonType;
use Application_Model_Entity_System_ContractorStatus as ContractorStatus;
use Application_Model_Entity_System_DivisionTitle as DivisionTitle;
use Application_Model_Entity_System_SystemValues as SystemValues;

/**
 * @method Application_Model_Entity_Collection_Entity_ContractorTemp getCollection()
 * @method Application_Model_Resource_Entity_ContractorTemp getResource()
 */
class Application_Model_Entity_Entity_ContractorTemp extends Application_Model_Base_TempEntity
{
    use Application_Model_ContactTrait;
    use Application_Model_Entity_Entity_ContractorVendorTrait;

    protected $parentEntity;
    /** @var Crypt */
    protected $crypt;
    final public const SSN_LENGTH = 9;
    final public const TAX_ID_LENGTH = 9;

    public function __construct()
    {
        parent::__construct();
        $this->crypt = new Crypt();
    }

    public function _beforeSave(): self
    {
        if ($this->checkErrors()) {
            $this->check();
        }
        parent::_beforeSave();
        if (!$this->getId()) {
            $key = User::getCurrentUser()->getCarrierKey();
            $this->setSocialSecurityId($this->crypt->encrypt($this->getSocialSecurityId(), $key));
            $this->setTaxId($this->crypt->encrypt($this->getTaxId(), $key));
        }

        return $this;
    }

    public function check(): self
    {
        if (!$this->getId()) {
            $this
                ->setupDivision()
                ->setupSettlementGroup()
                ->setupCode()
                ->setupContractor()
                ->setupFirstName()
                ->setupLastName()
                ->setupContactPersonType()
                ->setupTaxIdAndSocialSecurityId()
                ->setupDob()
                ->setupStateOfOperation()
                ->setupExpires()
                ->setupStatus()
                ->setupStartDate()
                ->setupTerminationDate()
                ->setupRehireDate()
                ->setupCorrespondenceMethod()
                ->setupBookkeepingTypeId()
                ->setupStatusId();
        }

        return $this;
    }

    private function setupDivision(): self
    {
        if (!$this->getDivision()) {
            $this->addJsonError(
                'Division is required and can not be empty (invalid Division)',
                $this->colDivision()
            );

            return $this;
        }

        $divisions = $this->getDivisions();
        $divisionId = $divisions[$this->getDivision()] ?? null;
        if (!$divisionId) {
            $this->addJsonError(
                'Division code not found (invalid Division code)',
                $this->colDivision()
            );

            return $this;
        }

        $acceptableDivisions = (new DivisionTitle())->getALl();
        $isTestDivision = $this->isTestValue($this->getDivision());
        if (!in_array($this->getDivision(), $acceptableDivisions) && !$isTestDivision) {
            $this->addJsonError(
                'Division is invalid (acceptable: ' . implode(', ', $acceptableDivisions) . ')',
                $this->colDivision()
            );

            return $this;
        }

        $this->setDivisionId($divisionId);
        $currentDivisionId = $this->getCurrentDivisionId();
        if ($currentDivisionId !== (int) $this->getDivisionId()) {
            $this->addWarning('Division is different from the current one');
        }

        return $this;
    }

    private function setupSettlementGroup(): self
    {
        if (!$this->getSettlementGroupId()) {
            $this->addJsonError(
                'Settlement Group is required and can not be empty (invalid Settlement Group)',
                $this->colSettlementGroupId()
            );

            return $this;
        }

        $settlementGroups = $this->getSettlementGroups();
        $settlementGroup = $settlementGroups[$this->getSettlementGroupId()] ?? null;
        if (!$settlementGroup) {
            $this->addJsonError(
                'Settlement Group not found (invalid Settlement Group)',
                $this->colSettlementGroupId()
            );

            return $this;
        }

        if ($settlementGroup['divisionName'] !== $this->getDivision()) {
            $this->addJsonError(
                'Settlement Group ' . $this->getSettlementGroupId() . ' is not associated with Division ' . $this->getDivision(),
                $this->colSettlementGroupId()
            );
        }

        $this->setSettlementGroupId($settlementGroup['id']);

        return $this;
    }

    private function setupCode(): self
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

    private function setupContractor(): self
    {
        if (!trim((string) $this->getCompanyName())) {
            $this->addJsonError(
                'Contractor is required and can not be empty (invalid Contractor)',
                $this->colCompanyName()
            );
        }

        return $this;
    }

    private function setupFirstName(): self
    {
        if (!trim((string) $this->getFirstName())) {
            $this->addJsonError(
                'First Name is required and can not be empty (invalid First Name)',
                $this->colFirstName()
            );
        }

        return $this;
    }

    private function setupLastName(): self
    {
        if (!trim((string) $this->getLastName())) {
            $this->addJsonError(
                'Last Name is required and can not be empty (invalid Last Name)',
                $this->colLastName()
            );
        }

        return $this;
    }

    private function setupContactPersonType(): self
    {
        if (!$this->getContactPersonType()) {
            $this->addJsonError(
                'Contact Person Type is required and can not be empty (invalid Status)',
                $this->colContactPersonType()
            );

            return $this;
        }

        match (mb_strtolower((string) $this->getContactPersonType())) {
            'owner' => $this->setContactPersonType(ContractorPersonType::TYPE_OWNER),
            'representative' => $this->setContactPersonType(ContractorPersonType::TYPE_REPRESENTATIVE),
            default => $this->addJsonError(
                'Contact Person Type is invalid (acceptable: Owner/Representative)',
                $this->colContactPersonType()
            ),
        };

        return $this;
    }

    private function setupDob(): self
    {
        $this->checkDate($this->getDob(), $this->colDob());

        return $this;
    }

    private function setupExpires(): self
    {
        $this->checkDate($this->getExpires(), $this->colExpires());

        return $this;
    }

    private function setupStartDate(): self
    {
        $this->checkDate($this->getStartDate(), $this->colStartDate(), true);

        return $this;
    }

    private function setupTerminationDate(): self
    {
        $isRequiredTerminationDate = $this->getStatus() !== ContractorStatus::STATUS_ACTIVE;
        $this->checkDate($this->getTerminationDate(), $this->colTerminationDate(), $isRequiredTerminationDate);

        return $this;
    }

    private function setupRehireDate(): self
    {
        $this->checkDate($this->getRehireDate(), $this->colRehireDate());

        return $this;
    }

    private function setupTaxIdAndSocialSecurityId(): self
    {
        if ((!$this->getTaxId() && !$this->getSocialSecurityId())
            || ($this->getTaxId() && $this->getSocialSecurityId())) {
            $this->addJsonError('Contractor can have either Tax ID or Social Security #', $this->colTaxId());
            $this->addJsonError('Contractor can have either Tax ID or Social Security #', $this->colSocialSecurityId());
        }

        if ($this->getTaxId()) {
            if (self::TAX_ID_LENGTH !== strlen((string) $this->getTaxId())
                || !filter_var($this->getTaxId(), FILTER_VALIDATE_INT)) {
                $this->addJsonError(
                    'Tax ID is invalid (Should be last ' . self::TAX_ID_LENGTH . ' digit of Tax ID)',
                    $this->colTaxId()
                );
            }
        }

        if ($this->getSocialSecurityId()) {
            if (self::SSN_LENGTH !== strlen((string) $this->getSocialSecurityId())
                || !filter_var($this->getSocialSecurityId(), FILTER_VALIDATE_INT)) {
                $this->addJsonError(
                    'Social Security # is invalid (Should be last ' . self::SSN_LENGTH . ' digit of Social Security #)',
                    $this->colSocialSecurityId()
                );
            }
        }

        return $this;
    }

    private function setupStateOfOperation(): self
    {
        if ($this->getStateOfOperation() && !in_array($this->getStateOfOperation(), SystemValues::getStates())) {
            $this->addJsonError('State of Issuance is invalid (Example: CA, NY, etc.)', $this->colStateOfOperation());
        }

        return $this;
    }

    public function setupStatus(): self
    {
        if (!$this->getStatus()) {
            $this->addJsonError('Status is required and can not be empty (invalid Status)', $this->colStatus());

            return $this;
        }

        match (mb_strtolower((string) $this->getStatus())) {
            'active' => $this->setStatus(ContractorStatus::STATUS_ACTIVE),
            'terminated' => $this->setStatus(ContractorStatus::STATUS_TERMINATED),
            default => $this->addJsonError('Status is invalid (acceptable: Active/Terminated)', $this->colStatus()),
        };

        return $this;
    }

    private function setupBookkeepingTypeId(): self
    {
        if ($this->getBookkeepingTypeId()) {
            match (mb_strtolower(trim((string) $this->getBookkeepingTypeId()))) {
                'atbs' => $this->setBookkeepingTypeId(BookkeepingType::TYPE_ATBS),
                'equinox' => $this->setBookkeepingTypeId(BookkeepingType::TYPE_EQUINOX),
                default => $this->addJsonError(
                    'Bookkeeping Service is invalid (acceptable: ATBS/Equinox)',
                    $this->colBookkeepingTypeId()
                ),
            };
        }

        return $this;
    }

    public function setupCorrespondenceMethod(): self
    {
        if (!$this->getCorrespondenceMethod()) {
            $this->addJsonError(
                'Correspondence Method is required and can not be empty (invalid Correspondence Method)',
                $this->colCorrespondenceMethod()
            );

            return $this;
        }

        match (mb_strtolower((string) $this->getCorrespondenceMethod())) {
            'yes' => $this->setCorrespondenceMethod(ContactType::TYPE_EMAIL),
            'no' => $this->setCorrespondenceMethod(ContactType::TYPE_CARRIER_DISTRIBUTES),
            default => $this->addJsonError(
                'Invalid Correspondence Method (acceptable: Yes/No)',
                $this->colCorrespondenceMethod()
            ),
        };

        return $this;
    }

    private function getExistCodes(): array
    {
        $cacheKey = 'existCodes' . $this->getDivisionId();
        if ($existCodesFromCache = Application_Model_Cache::load($cacheKey)) {
            return $existCodesFromCache;
        }
        $contractorCodes = (new Contractor())
            ->getCollection()
            ->addNonDeletedFilter()
            ->addFilter('carrier_id', $this->getDivisionId())
            ->getField('code');

        Application_Model_Cache::save($cacheKey, $contractorCodes);

        return $contractorCodes;
    }

    private function getExistTempCodes(): array
    {
        return Application_Model_Cache::load('existTempCodes') ?: [];
    }

    private function getDivisions(): array
    {
        if ($divisionsFromCache = Application_Model_Cache::load('divisions')) {
            $this->getLogger()->info('Contractor Temp. Get Divisions from cache');

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
        $this->getLogger()->info('Contractor Temp. Get Divisions from db');

        return $divisions;
    }

    private function getSettlementGroups(): array
    {
        if ($settlementGroupsFromCache = Application_Model_Cache::load('settlementGroups')) {
            return $settlementGroupsFromCache;
        }

        $items = (new Application_Model_Entity_Settlement_Group())
            ->getCollection()
            ->addNonDeletedFilter()
            ->getItems();

        $settlementGroups = [];
        foreach ($items as $item) {
            $settlementGroups[$item->getCode()] = [
                'id' => $item->getId(),
                'divisionName' => $item->getDivisionName(),
            ];
        }
        Application_Model_Cache::save('settlementGroups', $settlementGroups);

        return $settlementGroups;
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

    public function approve()
    {
        $this->parentEntity = $this->getResource()->getParentEntity();
        if (!$this->getStartDate()) {
            $this->setStartDate(null);
        }
        if (!$this->getRehireDate()) {
            $this->setRehireDate(null);
        }
        if (!$this->getTerminationDate()) {
            $this->setTerminationDate();
        }
        $this->parentEntity->setData($this->getData());
        $this->parentEntity->unsId();
        $this->parentEntity->save();

        return $this;
    }

    public function getParentEntity()
    {
        return $this->parentEntity;
    }

    public function getControllerName()
    {
        return 'contractors_index';
    }

    public function getExportCollection($idOrFilters = null)
    {
        if ($idOrFilters['isTemp'] ?? false) {
            $entity = new ContractorTemp();
            unset($idOrFilters['isTemp']);
            $collection = $entity
                ->getCollection()
                ->addSettlementGroup()
                ->addTempStatusInfo()
                ->setOrder('id', Application_Model_Base_Collection::SORT_ORDER_ASC);
            $this->applyFilters($collection, $idOrFilters);

            return $collection;
        }

        $entity = new Contractor();
        if (!is_array($idOrFilters) && (int)$idOrFilters) {
            $collection = [$entity->load($idOrFilters)];
        } else {
            $collection = $entity->getCollection()->addNonDeletedFilter();
            $user = User::getCurrentUser();
            if ($user->isContractor()) {
                $collection->addFilter('id', $user->getEntity()->getId());
            } else {
                $collection->addCarrierFilter()->vendorFilter();
            }
            $this->applyFilters($collection, $idOrFilters);
        }

        return $collection;
    }
}
