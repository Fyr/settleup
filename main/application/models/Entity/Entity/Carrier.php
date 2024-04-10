<?php

use Application_Model_Entity_Deductions_Setup as DeductionSetup;
use Application_Model_Entity_Payments_Setup as PaymentSetup;
use Application_Model_Entity_System_ContractorStatus as ContractorStatus;
use Application_Model_Entity_System_SettlementCycleStatus as CycleStatus;
use Application_Model_Entity_System_SystemValues as SystemValues;

/**
 * @method $this staticLoad($id, $field = null)
 * @method Application_Model_Entity_Collection_Entity_Carrier getCollection()
 * @method Application_Model_Resource_Entity_Carrier getResource()
 */
class Application_Model_Entity_Entity_Carrier extends Application_Model_Entity_Entity_Base
{
    use Application_Model_ContactTrait;
    use Application_Plugin_Messager;

    protected $_entityType = Application_Model_Entity_Entity_Type::TYPE_DIVISION;
    //TODO replace this property after refactoring
    protected $_titleColumn = 'name';
    protected $rule;
    public $_name = 'carrier';
    final public const AUTO_CREATE_CONTRACTOR_USER = 1;
    final public const MANUALLY_CREATE_CONTRACTOR_USER = 0;

    /**
     * returns payments collection
     *
     * @return array
     */
    public function getPayments()
    {
        //@todo change to real data
        $paymentsModel = new Application_Model_Entity_Payments_Payment();

        return $paymentsModel->getCollection();
    }

    /**
     * returns contractors collection
     *
     * @return Application_Model_Entity_Collection_Entity_contractor
     */
    public function getContractors($settlementGroupId)
    {
        $contractorsCollections = (new Application_Model_Entity_Entity_Contractor())->getCollection()
            ->addFilter('carrier_id', $this->getEntityId())
            ->addFilter('settlement_group_id', $settlementGroupId)
        ;
        if ($contractorsCollections->count()) {
            return $contractorsCollections->getItems();
        }

        return [];
    }

    /**
     * @return Application_Model_Base_Collection
     */
    public function getActiveContractors()
    {
        $contractorsModel = new Application_Model_Entity_Entity_Contractor();

        return $contractorsModel->getCollection()->addFilterByActiveCarrierContractor($this->getEntityId());
    }

    /**
     * @return array
     */
    public function getActiveContractorsArray()
    {
        return $this->getActiveContractors()->getField('entity_id');
    }

    /**
     * returns active settlement cycle
     *
     * @return Application_Model_Entity_Settlement_Cycle
     */
    public function getActiveSettlementCycle()
    {
        //todo replace code here from method getFirstNotClosedSettlementCycle
        /*$cycle = new Application_Model_Entity_Settlement_Cycle();
        $collection = $cycle->getCollection()
                       ->addFilter('carrier_id',$this->getEntityId())
                       ->addFilter('cycle_start_date', date('Y-m-d') , '<=')
                       ->addFilter('cycle_close_date', date('Y-m-d') , '>=')
        ;

        return $collection->getFirstItem();*/
        return $this->getFirstNotClosedSettlementCycle();
    }

    /**
     * returns previous settlement cycle
     *
     * @return Application_Model_Entity_Settlement_Cycle
     */
    public function getPreviousSettlementCycle()
    {
        $previousCycle = new Application_Model_Entity_Settlement_Cycle();
        $activeCycle = $this->getActiveSettlementCycle();
        $collection = $previousCycle->getCollection()->addFilter('carrier_id', $this->getEntityId())->addFilter(
            'cycle_close_date',
            $activeCycle->getCycleStartDate()
        );

        return $collection->getFirstItem();
    }

    public function getCurrentCarrier()
    {
        return $this;
    }

    /**
     * @return Application_Model_Entity_Entity_Contractor
     */
    public function getCurrentContractor()
    {
        $contractorEntity = new Application_Model_Entity_Entity_Contractor();
        $contractorId = Application_Model_Entity_Accounts_User::getCurrentUser()->getLastSelectedContractor();
        if ($contractorId) {
            return $contractorEntity->load($contractorId);
        }

        return null;
    }

    /**
     * @return Application_Model_Entity_Settlement_Cycle
     */
    public function getFirstNotClosedSettlementCycle()
    {
        $cycle = new Application_Model_Entity_Settlement_Cycle();
        $cycleCollection = $cycle->getCollection()->addFilter('carrier_id', $this->getEntityId())->addFilter(
            'status_id',
            CycleStatus::APPROVED_STATUS_ID,
            '<>'
        )->addNonDeletedFilter();

        return $cycleCollection->getFirstItem();
    }

    public function getNotVerifiedSettlementCycle()
    {
        $cycle = new Application_Model_Entity_Settlement_Cycle();
        $cycleCollection = $cycle->getCollection()->addFilter('carrier_id', $this->getEntityId())->addFilter(
            'status_id',
            CycleStatus::NOT_VERIFIED_STATUS_ID
        )->addNonDeletedFilter();

        return $cycleCollection->getFirstItem();
    }

    /**
     * @return Application_Model_Entity_Settlement_Cycle
     */
    public function getLastClosedSettlementCycle()
    {
        $cycle = new Application_Model_Entity_Settlement_Cycle();
        $cycleCollection = $cycle->getCollection()->addFilter('carrier_id', $this->getEntityId())->addFilter(
            'status_id',
            CycleStatus::APPROVED_STATUS_ID
        )->addNonDeletedFilter();

        return $cycleCollection->getLastItem();
    }

    public function getCycles()
    {
        $cycleEntity = new Application_Model_Entity_Settlement_Cycle();
        $cycleCollection = $cycleEntity->getCollection()->addCarrierFilter()->addSettlementGroupFilter();

        return $cycleCollection;
    }

    public function getCycleRule()
    {
        if (!$this->rule) {
            if ($carrierId = $this->getEntityId()) {
                $this->rule = (new Application_Model_Entity_Settlement_Rule())->load($carrierId, 'carrier_id');
            } else {
                if ($carrierId = Application_Model_Entity_Accounts_User::getCurrentUser()->getSelectedCarrier(
                )->getEntityId()) {
                    $this->rule = (new Application_Model_Entity_Settlement_Rule())->load($carrierId, 'carrier_id');
                } else {
                    $this->rule = (new Application_Model_Entity_Settlement_Rule());
                }
            }
        }

        return $this->rule;
    }

    public function _afterSave()
    {
        parent::_afterSave();
        $currentCarrier = Application_Model_Entity_Accounts_User::getCurrentUser()->getSelectedCarrier();
        if ($this->getId() == $currentCarrier->getId()) {
            $currentCarrier->setData($this->getData());
        }

        return $this;
    }

    public function _beforeSave()
    {
        if ($this->getOriginalData(
            'create_contractor_type'
        ) == self::MANUALLY_CREATE_CONTRACTOR_USER && $this->getCreateContractorType(
        ) == self::AUTO_CREATE_CONTRACTOR_USER) {
            $contractors = (new Application_Model_Entity_Entity_Contractor())->getCollection()->addFilter(
                'carrier_id',
                $this->getEntityId()
            )->addNondeletedFilter()->getItems();
            foreach ($contractors as $contractor) {
                $contractor->createNewUser(false);
                if ($contractor->hasMessages()) {
                    $this->addMessages($contractor->getMessages()['default']);
                }
            }
        }

        return parent::_beforeSave();
    }

    /**
     * @return Application_Model_Entity_Accounts_Escrow
     */
    public function getEscrowAccount()
    {
        $account = new Application_Model_Entity_Accounts_Escrow();
        $account->load($this->getEntityId(), 'carrier_id');
        $account->setCarrierTitle($this->getName());

        return $account;
    }

    public function getCode()
    {
        return $this->getId();
    }

    public function checkPermissions(): bool
    {
        $user = Application_Model_Entity_Accounts_User::getCurrentUser();
        if ($user->isAdminOrSuperAdmin() || $user->isManager()) {
            return true;
        }

        return false;
    }

    public function createPaymentTemplates(): self
    {
        $templates = (new PaymentSetup())
            ->getCollection()
            ->addCarrierFilter($this->getEntityId())
            ->addMasterFilter()
            ->addNonDeletedFilter()
            ->getItems();
        /** @var PaymentSetup $template */
        foreach ($templates as $template) {
            $this->getLogger()->info('Create Individual Compensation Template for SetupId: ' . $template->getId());
            $template->createIndividualTemplates();
        }

        return $this;
    }

    public function createDeductionTemplates(): self
    {
        $templates = (new DeductionSetup())
            ->getCollection()
            ->addMasterFilter()
            ->addProviderIdFilter($this->getEntityId())
            ->addNonDeletedFilter()
            ->getItems();
        /** @var DeductionSetup $template */
        foreach ($templates as $template) {
            $this->getLogger()->info('Create Individual Deduction Template for SetupId: ' . $template->getId());
            $template->createIndividualTemplates();
        }

        return $this;
    }

    public function purgeData()
    {
        $result = false;
        if ($id = (int)$this->getEntityId()) {
            $sql = 'CALL purgeCarrierData(?)';
            $stmt = $this->getResource()->getAdapter()->prepare($sql);
            $stmt->bindParam(1, $id);
            $result = $stmt->execute();

            $user = Application_Model_Entity_Accounts_User::getCurrentUser();
            if ($user->getSelectedCarrier()->getId() == $this->getId()) {
                $user->resetCarrier();
                $user->setSettlementCycle(null);
            }
        }

        return $result;
    }

    /**
     * @return Application_Model_Entity_CustomFieldNames
     */
    public function getCustomFieldNames()
    {
        return Application_Model_Entity_CustomFieldNames::staticLoad($this->getEntityId(), 'carrier_id')->setCarrierId(
            $this->getEntityId()
        );
    }

    public function getAllDivisions(): array
    {
        $db = $this->getResource()->getAdapter();
        $select = $db->select()
            ->from(['division' => $this->_name], ['*'])
            ->join(['entity' => 'entity'], 'division.entity_id = entity.id', [])
            ->where('division.status = ?', ContractorStatus::STATUS_ACTIVE)
            ->where('entity.deleted = ?', (string) SystemValues::NOT_DELETED_STATUS);
        $divisions = $db->fetchAll($select);
        $result = [];
        foreach ($divisions as $division) {
            $result[$division['id']] = $division;
        }

        return $result;
    }

    public function createIndividualTemplatesByDivisionIds(array $divisionIds): void
    {
        foreach ($divisionIds as $divisionId) {
            $divisionEntity = new self();
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
}
