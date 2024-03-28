<?php

use Application_Model_Entity_System_ReserveTransactionTypes as ReserveTransactionTypes;
use Application_Model_Entity_System_SettlementCycleStatus as CycleStatus;

class Application_Form_Account_Reserve_Transaction extends Application_Form_Base
{
    protected $_raContractor;
    protected $_raVendor;
    protected $_contractor;
    protected $_powerunit;

    public function init()
    {
        $this->setName('reserve_account_transaction');
        parent::init();

        $id = (new Zend_Form_Element_Text('id'))
             ->setLabel('ID')
             ->setAttrib('readonly', 'readonly');

        $reserveAccountId = new Zend_Form_Element_Hidden('reserve_account_vendor');
        $reserveAccountContractorId = new Zend_Form_Element_Hidden('reserve_account_contractor');
        $typeId = new Zend_Form_Element_Hidden('type');
        $approvedBy = new Zend_Form_Element_Hidden('approved_by');
        $createdBy = new Zend_Form_Element_Hidden('created_by');
        $contractorId = new Zend_Form_Element_Hidden('contractor_id');

        $contractorCode = (new Zend_Form_Element_Text('contractor_code'))
            ->setLabel('Contractor Code');

        $typeTitle = (new Zend_Form_Element_Text('type_title'))
            ->setLabel('Type')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('readonly', 'readonly');

        $description = (new Zend_Form_Element_Text('description'))
            ->setLabel('Description ')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setRequired(true);

        $powerunitCode = (new Zend_Form_Element_Text('powerunit_code'))
            ->setLabel('Power Unit')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $reference = (new Zend_Form_Element_Text('reference'))
            ->setLabel('Reference')
            ->addFilter('StripTags')
            ->addFilter('StringTrim');

        $amount = (new Application_Form_Element_Money('amount'))
            ->setLabel('Amount')
            ->setRequired(true);

        $remainingBalance = (new Application_Form_Element_Money('balance'))
            ->setLabel('Remaining Balance');

        $balance = (new Application_Form_Element_Text('current_balance'))
            ->setLabel('Current Balance')
            ->setAttrib('readonly', 'readonly');

        $approvedDatetime = (new Zend_Form_Element_Text('approved_datetime'))
            ->setLabel('Approved Date ')
            ->setAttrib('readonly', 'readonly');

        $approvedByTitle = (new Zend_Form_Element_Text('approved_by_title'))
            ->setLabel('Approved By')
            ->setAttrib('readonly', 'readonly');

        $createdDatetime = (new Zend_Form_Element_Text('created_datetime'))
            ->setLabel('Created Date')
            ->setAttrib('readonly', 'readonly');

        $createdByTitle = (new Zend_Form_Element_Text('created_by_title'))
            ->setLabel('Created By')
            ->setAttrib('readonly', 'readonly');

        $sourceId = (new Zend_Form_Element_Text('source_id'))
            ->setLabel('Source ')
            ->setAttrib('disabled', 'disabled');

        $statusTitle = (new Zend_Form_Element_Text('status_title'))
            ->setLabel('Status')
            ->setAttrib('readonly', 'readonly');

        $accountName = (new Zend_Form_Element_Text('reserve_account_vendor_title'))
            ->setLabel('Reserve Account')
            ->setAttrib('readonly', 'readonly');

        $companyName = (new Zend_Form_Element_Text('company_name'))
            ->setLabel('Contractor')
            ->setAttrib('readonly', 'readonly');

        $reserveCode = (new Zend_Form_Element_Text('reserve_code'))
            ->setLabel('Reserve Code')
            ->setAttrib('readonly', 'readonly');

        $settlementCycleId = (new Zend_Form_Element_Select('settlement_cycle_id'))
            ->setLabel('Settlement Cycle')
            ->setRegisterInArrayValidator(false);

        $settlementStatus = (new Zend_Form_Element_Text('settlement_status'))
            ->setLabel('Status')
            ->setAttrib('readonly', 'readonly');

        $adjustmentType = (new Zend_Form_Element_Select('adjustment_type'))
            ->setLabel('Adjustment Type')
            ->setMultiOptions([
                ReserveTransactionTypes::ADJUSTMENT_DECREASE => 'Decrease',
                ReserveTransactionTypes::ADJUSTMENT_INCREASE => 'Increase',
            ]);

        $contractorAccount = (new Zend_Form_Element_Text('reserve_account_contractor_title'))
            ->setLabel('Contractor Reserve Account')
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('href', '#reserve_account_contractor_modal')
            ->setAttrib('data-toggle', 'modal')
            ->setAttrib('readonly', 'readonly');

        $deductionId = (new Zend_Form_Element_Select('deduction_id'))
            ->setLabel('Deduction')
            ->setMultiOptions(['0' => 'not selected...']);

        $this->addElements([
            $id,
            $contractorCode,
            $reserveCode,
            $settlementCycleId,
            $companyName,
            $typeTitle,
            $description,
            $accountName,
            $amount,
            $balance,
            $approvedDatetime,
            $approvedByTitle,
            $createdDatetime,
            $createdByTitle,
            $sourceId,
            $statusTitle,
            $settlementStatus,
            $adjustmentType,
            $reserveAccountId,
            $typeId,
            $approvedBy,
            $createdBy,
            $contractorId,
            $reserveAccountContractorId,
            $contractorAccount,
            $deductionId,
            $remainingBalance,
            $powerunitCode,
            $reference,
        ]);

        $this->setDefaultDecorators([
            'id',
            'contractor_code',
            'company_name',
            'reserve_account_vendor_title',
            'description',
            'vendor_code',
            'type_title',
            'deduction_id',
            'amount',
            'reserve_code',
            'current_balance',
            'adjusted_balance',
            'approved_datetime',
            'settlement_cycle_id',
            'approved_by_title',
            'created_datetime',
            'created_by_title',
            'source_id',
            'status_title',
            'settlement_status',
            'adjustment_type',
            'reserve_account_contractor_title',
            'deduction_id',
            'balance',
            'powerunit_code',
            'reference',
        ]);

        $this->addSubmit('Save');
    }

    public function setupForEditAction()
    {
        $this->defaultSetup();

        $cycleEntity = new Application_Model_Entity_Settlement_Cycle();
        $cycleEntity->load($this->settlement_cycle_id->getValue());

        $this->settlement_cycle_id
            ->setMultiOptions(
                $cycleEntity->getCyclePeriods(Application_Model_Entity_Settlement_Cycle::ALL_FILTER_TYPE_ASC)
            )
            ->setAttrib('disabled', 'disabled');

        if (!$this->settlement_status->getValue()) {
            $this->settlement_status->setValue($cycleEntity->getStatus()->getTitle());
        }

        if ($cycleEntity->getStatusId() >= CycleStatus::APPROVED_STATUS_ID
            || (
                $cycleEntity->getStatusId() == CycleStatus::PROCESSED_STATUS_ID
                && in_array(
                    $this->type->getValue(),
                    [ ReserveTransactionTypes::ADJUSTMENT_DECREASE, ReserveTransactionTypes::ADJUSTMENT_INCREASE ]
                )
            )
        ) {
            $this->setFormReadOnly();
        }
        if ($cycleEntity->getStatusId() >= CycleStatus::VERIFIED_STATUS_ID) {
            if (in_array(
                $this->type->getValue(),
                [ ReserveTransactionTypes::ADJUSTMENT_INCREASE, ReserveTransactionTypes::ADJUSTMENT_DECREASE ]
            )
            ) {
                $reserveAccountHistory = new Application_Model_Entity_Accounts_Reserve_History();
                $reserveAccountHistory->load([
                    'reserve_account_id' => $this->reserve_account_contractor->getValue(),
                    'settlement_cycle_id' => $this->settlement_cycle_id->getValue(),
                ]);
                if ($reserveAccountHistory->getId()) {
                    $this->current_balance->setValue($reserveAccountHistory->getCurrentBalance());
                }
            }
        }

        if ($cycleEntity->getStatusId() >= CycleStatus::PROCESSED_STATUS_ID) {
            if (in_array(
                $this->type->getValue(),
                [ ReserveTransactionTypes::ADJUSTMENT_INCREASE, ReserveTransactionTypes::ADJUSTMENT_DECREASE ]
            )
            ) {
                // if we remove submit button, should form be readonly?
                $this->removeElement('submit');
            }
        }

        if ($this->type->getValue() != ReserveTransactionTypes::WITHDRAWAL
            || $this->created_by_title->getValue() == 'System'
        ) {
            $this->removeElement('deduction_id');
        }

        if ($this->source_id->getValue()) {
            $this->source_id->setValue('Import');
        } else {
            $this->source_id->setValue('Manual');
        }

        if ($this->type->getValue() == ReserveTransactionTypes::CONTRIBUTION) {
            $this->amount->setLabel('Amount *, -$')->setRequired(false);
        }
    }

    public function setupForNewAction()
    {
        if (!$this->reserve_account_vendor->getValue()) {
            $this->reserve_account_vendor->setValue($this->getRAVendor()->getReserveAccountId());
            $this->reserve_account_vendor_title->setAttrib('readonly', 'readonly');
        }

        if (!$this->contractor_id->getValue()) {
            $this->contractor_id->setValue($this->getRAContractor()->getEntityId());
        } else {
            $this->reserve_account_contractor_title->setAttrib('readonly', null);
        }

        $this->defaultSetup();

        $cycleEntity = new Application_Model_Entity_Settlement_Cycle();
        $this->settlement_cycle_id->setMultiOptions(
            $cycleEntity->getCyclePeriods(
                Application_Model_Entity_Settlement_Cycle::ALL_FILTER_TYPE_ASC,
                Application_Model_Entity_Settlement_Cycle::ONLY_ACTIVE
            )
        );

        if ($this->source_id->getValue()) {
            $this->source_id->setValue('Import');
        } else {
            $this->source_id->setValue('Manual');
        }

        if ($this->type->getValue() != ReserveTransactionTypes::WITHDRAWAL
            || $this->created_by_title->getValue() == 'System'
        ) {
            $this->removeElement('deduction_id');
        }

        $this->settlement_status->setValue('Verified');
    }

    public function defaultSetup()
    {
        if (!$this->type->getValue()) {
            $this->type->setValue(ReserveTransactionTypes::ADJUSTMENT_INCREASE);
        }

        if ($this->type->getValue() >= ReserveTransactionTypes::ADJUSTMENT_DECREASE && !$this->id->getValue()) {
            $this->amount->setLabel('Adjustment Amount');
            $this->adjustment_type->setValue($this->type->getValue());
            if (!$this->current_balance->getValue()) {
                $this->current_balance->setValue($this->getRAContractor()->getCurrentBalance());
            }
        }

        if ($this->type->getValue() >= ReserveTransactionTypes::ADJUSTMENT_DECREASE && $this->id->getValue()) {
            $this->amount->setLabel('Adjustment Amount');
            $this->adjustment_type->setValue($this->type->getValue());
        }

        if (!$this->company_name->getValue()) {
            $this->company_name->setValue($this->getContractor()->getCompanyName());
            $this->contractor_code->setValue($this->getContractor()->getCode());
            $this->powerunit_code->setValue($this->getPowerUnit()->getCode());
        }

        if (!$this->reserve_account_vendor_title->getValue()) {
            $this->reserve_account_vendor_title->setValue($this->getRAVendor()->getAccountTitle());
        }

        if (!$this->reserve_account_contractor_title->getValue()) {
            $this->reserve_account_contractor_title->setValue($this->getRAContractor()->getAccountName());
        }

        if (!$this->reserve_code->getValue()) {
            $this->reserve_code->setValue($this->getRAVendor()->getVendorReserveCode());
        }

        $value = '';
        switch ($this->type->getValue()) {
            case ReserveTransactionTypes::CONTRIBUTION:
                $value = 'Contribution';
                break;
            case ReserveTransactionTypes::WITHDRAWAL:
                $value = 'Withdrawal';
                break;
            case ReserveTransactionTypes::ADJUSTMENT_DECREASE:
            case ReserveTransactionTypes::ADJUSTMENT_INCREASE:
            case '':
                $value = 'Adjustment';
                break;
        }
        $this->type_title->setValue($value);

        $this->checkPermissions();
    }

    public function checkPermissions()
    {
        $currUser = Application_Model_Entity_Accounts_User::getCurrentUser();
        if (!$currUser->hasPermission(Application_Model_Entity_Entity_Permissions::SETTLEMENT_DATA_MANAGE)
            || $currUser->isVendor()
        ) {
            $this->setFormReadOnly();
        }
    }

    protected function setFormReadOnly()
    {
        foreach ($this->getElements() as $element) {
            $element->setAttrib('readonly', 'readonly');
        }
        $this->removeElement('submit');
    }

    public function getContractor()
    {
        if (!$this->_contractor) {
            $this->_contractor = new Application_Model_Entity_Entity_Contractor();
            $this->_contractor->load($this->contractor_id->getValue(), 'entity_id');
        }

        return $this->_contractor;
    }

    public function getRAContractor()
    {
        if (!$this->_raContractor) {
            $this->_raContractor = (new Application_Model_Entity_Accounts_Reserve_Contractor())->load(
                $this->reserve_account_contractor->getValue(),
                'reserve_account_id'
            );
        }

        return $this->_raContractor;
    }

    public function getRAVendor()
    {
        if (!$this->_raVendor) {
            $id = $this->reserve_account_vendor->getValue();
            $vendor = new Application_Model_Entity_Accounts_Reserve_Vendor();
            if (!$id) {
                $this->_raVendor = $vendor->load(
                    $this->getRAContractor()->getReserveAccountVendorId()
                );
            } else {
                $this->_raVendor = $vendor->load(
                    $this->reserve_account_vendor->getValue(),
                    'reserve_account_id'
                );
            }
        }

        return $this->_raVendor;
    }

    public function setupDeductionId($contractorId, $cycleId)
    {
        if ($cycleId) {
            $this->deduction_id->addMultiOptions(
                (new Application_Model_Entity_Deductions_Deduction())
                    ->getResource()
                    ->getOptions(
                        'description',
                        'contractor_id = '.$contractorId.' AND settlement_cycle_id='.$cycleId.' AND deleted = 0'
                    )
            );
        }

        return $this;
    }

    public function getPowerUnit()
    {
        if (!$this->_powerunit) {
            $this->_powerunit = new Application_Model_Entity_Powerunit_Powerunit();
            $contractor_id = $this->getContractor()->getEntityId();
            $this->_powerunit->load($contractor_id, 'contractor_id');
        }

        return $this->_powerunit;
    }
}
