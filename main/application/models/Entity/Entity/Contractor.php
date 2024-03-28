<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_ContractorVendor as ContractorVendor;
use Application_Model_Entity_System_ContractorStatus as ContractorStatus;
use Application_Model_Entity_System_ReserveTransactionTypes as ReserveTransactionTypes;
use Application_Model_Entity_System_SystemValues as SystemValues;
use Application_Model_Entity_System_VendorStatus as VendorStatus;

/**
 * @method Application_Model_Entity_Collection_Entity_Contractor getCollection()
 * @method Application_Model_Resource_Entity_Contractor getResource()
 */
class Application_Model_Entity_Entity_Contractor extends Application_Model_Entity_Entity_Base
{
    use Application_Model_ContactTrait;
    use Application_Model_Entity_Entity_ContractorVendorTrait;
    use Application_Model_Entity_Permissions_CarrierTrait;
    use Application_Plugin_Messager;

    final public const PRIORITY_MASTER = 1;
    final public const PRIORITY_CUSTOM = 2;
    public const AZURE_CONTAINER_NAME = 'contractor';

    protected $_entityType = Application_Model_Entity_Entity_Type::TYPE_CONTRACTOR;

    //TODO replace this property after refactoring
    protected $_titleColumn = 'company_name';
    public $status = false;
    public $_name = 'contractor';

    /**
     * returns deductions collection
     *
     * @return array
     */
    public function getDeductions()
    {
        //@todo change to real data{
        $deductionsModel = new Application_Model_Entity_Deductions_Deduction();

        return $deductionsModel->getCollection();
    }

    /**
     * @param $settlementCycle Application_Model_Entity_Settlement_Cycle
     * @param $paymentSum float
     * @return $this
     */
    public function updateReserveAccount($settlementCycle, $paymentSum)
    {
        $reserveAccountContractorModel = new Application_Model_Entity_Accounts_Reserve_Contractor();

        $reserveAccounts = $reserveAccountContractorModel->getCollection()->addFilter(
            'contractor_entity_id',
            $this->getEntityId()
        )->addActiveVendorFilter($this)->addNonDeletedFilter()->addFilter(
            'reserve_account.current_balance',
            'reserve_account.min_balance',
            '<',
            false
        )->setOrder('priority', 'ASC')->getItems();

        foreach ($reserveAccounts as $reserveAccount) {
            $reserveTransactionModel = (new Application_Model_Entity_Accounts_Reserve_Transaction())
                ->setReserveAccountContractor($reserveAccount->getReserveAccountContractorId())
                ->setReserveAccountVendor($reserveAccount->getVendorReserveAccountId())
                ->setContractorId($this->getEntityId())
                ->setSettlementCycleId($settlementCycle->getId())
                ->setDescription($reserveAccount->getDescription())
                ->setCreatedDatetime((new DateTime())->format('Y-m-d'))
                ->setType(ReserveTransactionTypes::CONTRIBUTION)
                ->setAmount(
                    min(
                        (float) $reserveAccount->getContributionAmount(),
                        (float) $reserveAccount->getMinBalance() - (float) $reserveAccount->getCurrentBalance(),
                        $paymentSum
                    )
                )
                ->setCreatedBy(User::SYSTEM_USER);
            $paymentSum -= $reserveTransactionModel->getAmount();
            if ($reserveTransactionModel->getAmount()) {
                $reserveTransactionModel->save();
            }
        }

        return $this;
    }

    //TODO Replace the hardcode by real method
    public function getCarrier()
    {
        return $this->getCurrentCarrier();
    }

    /**
     * @param string $status New status
     * @return Application_Model_Entity_Entity_Contractor
     */
    public function changeStatus($status = ContractorStatus::STATUS_ACTIVE)
    {
        // if (!$this->getId() && !$this->getStartDate(
        // ) && $status == Application_Model_Entity_System_ContractorStatus::STATUS_ACTIVE) {
        //     $status = Application_Model_Entity_System_ContractorStatus::STATUS_INACTIVE;
        // }
        $this->status = $status;
        $this->save();

        return $this;
    }

    /**
     * @return Application_Model_Entity_Entity_Carrier
     */
    public function getCurrentCarrier()
    {
        $carrierEntity = new Application_Model_Entity_Entity_Carrier();

        if ($this->getCarrierId()) {
            $carrierEntity->load($this->getCarrierId(), 'entity_id');
        }

        return $carrierEntity;
    }

    /**
     * @return Application_Model_Entity_Entity_Contractor
     */
    public function getCurrentContractor()
    {
        $contractorEntity = new Application_Model_Entity_Entity_Contractor();
        $contractorId = User::getCurrentUser()->getLastSelectedContractor();
        if ($contractorId) {
            return $contractorEntity->load($contractorId);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getCompanyName();
    }

    public function getReserveAccountCurrentBalanceSum($cycleId)
    {
        $db = $this->getResource()->getAdapter();
        $select = $db->select()->from(
            ['rah' => 'reserve_account_history'],
            new Zend_Db_Expr('SUM(rah.current_balance) as cur_bal')
        )->joinLeft(
            ['ra' => 'reserve_account'],
            'rah.reserve_account_id = ra.id',
            []
        )->where('rah.settlement_cycle_id = ?', $cycleId)->where('ra.entity_id = ?', $this->getEntityId());
        $result = $db->fetchRow($select);

        return $result;
    }

    public function getReserveTransactionAmountSum($cycleId)
    {
        $db = $this->getResource()->getAdapter();
        $select = $db->select()
            ->from(
                ['rt' => 'reserve_transaction'],
                new Zend_Db_Expr('SUM(IF(type = 1, -rt.amount, rt.amount)) as amount_sum')
            )
            ->where('rt.settlement_cycle_id = ?', $cycleId)
            ->where('rt.contractor_id = ?', $this->getEntityId());

        return $db->fetchRow($select);
    }

    /**
     * check deductions
     *
     * @return bool
     */
    public function hasDeductions()
    {
        $deductionEntity = new Application_Model_Entity_Deductions_Deduction();
        $select = $deductionEntity->getResource()->select()->where('contractor_id = ?', $this->getEntityId())->where(
            'deleted = ?',
            0
        )->where('settlement_cycle_id IS NOT NULL')->limit(1);

        return (bool) $this->getResource()->getAdapter()->fetchRow($select);
    }

    public function removeRecurringDeductions()
    {
        $deductionEntity = new Application_Model_Entity_Deductions_Deduction();
        $select = 'contractor_id = ' . $this->getEntityId() . ' AND deleted = 0 AND settlement_cycle_id IS NULL';
        $deductionEntity->getResource()->delete($select);

        return $this;
    }

    public function removeRelatedData()
    {
        $result = false;
        if ($id = $this->getEntityId()) {
            $sql = 'CALL removeContractorRelatedData(?)';
            $stmt = $this->getResource()->getAdapter()->prepare($sql);
            $stmt->bindParam(1, $id);
            $result = $stmt->execute();
        }

        $racCollection = (new Application_Model_Entity_Accounts_Reserve_Contractor())->getCollection()->addFilter(
            'contractor_entity_id',
            $this->getEntityId()
        );
        foreach ($racCollection as $rac) {
            $rac->setDeleted(SystemValues::DELETED_STATUS)->save();
        }

        return $result;
    }

    /**
     * check payments
     *
     * @return bool
     */
    public function hasPayments()
    {
        $paymentEntity = new Application_Model_Entity_Payments_Payment();
        $select = $paymentEntity->getResource()->select()->where('contractor_id = ?', $this->getEntityId())->where(
            'deleted = ?',
            0
        )->where('settlement_cycle_id IS NOT NULL')->limit(1);

        return (bool) $this->getResource()->getAdapter()->fetchRow($select);
    }

    public function removeRecurringPayments()
    {
        $paymentEntity = new Application_Model_Entity_Payments_Payment();
        $select = 'contractor_id = ' . $this->getEntityId() . ' AND deleted = 0 AND settlement_cycle_id IS NULL';
        $paymentEntity->getResource()->delete($select);

        return $this;
    }

    /**
     * check transactions
     *
     * @return bool
     */
    public function hasTransactions()
    {
        $transactionEntity = new Application_Model_Entity_Accounts_Reserve_Transaction();
        $select = $transactionEntity->getResource()->select()->where('contractor_id = ?', $this->getEntityId())->where(
            'deleted = ?',
            0
        )->limit(1);

        return (bool) $this->getResource()->getAdapter()->fetchRow($select);
    }

    public function _beforeSave()
    {
        if (!$this->getId() && !$this->getStatus()) {
            $this->setStatus(ContractorStatus::STATUS_ACTIVE);
        }
        if (!$this->getCarrierId()) {
            $this->setCarrierId(
                User::getCurrentUser()->getSelectedCarrier()->getEntityId()
            );
        }

        if (!$this->getDeductionPriority()) {
            $this->setDeductionPriority(self::PRIORITY_MASTER);
        }

        $this->updateStatus();

        if (!$this->getBookkeepingTypeId()) {
            $this->setBookkeepingTypeId(null);
        }

        if ($this->getSettlementGroupId()) {
            $settlementGroup = (new Application_Model_Entity_Settlement_Group())
                ->getCollection()
                ->addFilter('id', $this->getSettlementGroupId())
                ->getFirstItem();
            $this->setCarrierId($settlementGroup->getDivisionEntityId());
        } else {
            $this->setSettlementGroupId(null);
        }


        parent::_beforeSave();

        return $this;
    }

    public function getGenderTitle()
    {
        if ($this->getGenderId() == SystemValues::GENDER_MALE) {
            $result = 'Male';
        } elseif ($this->getGenderId() == SystemValues::GENDER_FEMALE) {
            $result = 'Female';
        } else {
            $result = '-';
        }

        return $result;
    }

    public function createIndividualTemplates()
    {
        $this->getCarrier()->createPaymentTemplates();
        $this->getCarrier()->createDeductionTemplates();
    }

    public function getVendorStatus($vendorEntityId)
    {
        $contractorVendor = ContractorVendor::staticLoad([
            'contractor_id' => $this->getEntityId(),
            'vendor_id' => $vendorEntityId,
        ]);
        if ($contractorVendor->getId()) {
            return (int) $contractorVendor->getStatus();
        }

        return false;
    }

    /**
     * Automatically creates new accounts (or relates with existing) by email
     */
    public function createNewUser($checkCarrier = true)
    {
        if ($emails = $this->getContactEmails($checkCarrier)) {
            /**
             * Create new accounts
             */
            $toSend = [];
            $restService = new Application_Model_Rest();
            $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
            $loginUrl = $baseUrl . '/auth/login/';

            $additionalContacts = (new Application_Model_Entity_Entity_Contact_Info())->getCollection()->addFilter(
                $this->getLoadBy(),
                $this->getData($this->getLoadBy())
            )->addFilter('contact_type', Application_Model_Entity_Entity_Contact_Type::TYPE_EMAIL, '!=')->getItems(
            );

            foreach ($emails as $email) {
                $password = User::generatePassword();

                $userModel = new User();
                $userModel->setData([
                    'role_id' => Application_Model_Entity_System_UserRoles::CONTRACTOR_ROLE_ID,
                    'email' => $email,
                    'name' => implode(' ', [$this->getFirstName(), $this->getLastName()]),
                    'password' => $password,
                    'entity_id' => $this->getEntityId(),
                    'receive_notifications' => 1,
                ])->save();
                $userEntityModel = new Application_Model_Entity_Accounts_UserEntity();
                $userEntityModel->setData([
                    'user_id' => $userModel->getId(),
                    'entity_id' => $this->getEntityId(),
                ])->save();

                /**
                 * Create additional contacts
                 */
                foreach ($additionalContacts as $contact) {
                    $contact->addData([
                        'id' => null,
                        'entity_id' => null,
                        'user_id' => $userModel->getId(),
                    ])->save();
                }
                /**
                 * Create record on security server
                 */
                $restService->updateUser([
                    'id' => $userModel->getId(),
                    'role_id' => $userModel->getRoleId(),
                    'carrier_id' => $this->getCarrierId() ?: 0,
                    'carriers' => [$this->getCarrierId()],
                ]);

                $toSend[] = [
                    'name' => $userModel->getName(),
                    'loginUrl' => $loginUrl,
                    'baseUrl' => $baseUrl,
                    'email' => $email,
                    'password' => $password,
                ];
            }

            /**
             * Send mail for new accounts
             */
            foreach ($toSend as $target) {
                /** @var Zend_View $html */
                $html = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('view');

                $html->assign($target);
                $html->setScriptPath(APPLICATION_PATH . '/views/scripts/emails/');
                $body = $html->render('login-info.phtml');

                Application_Plugin_Mail::sendMail(
                    $target['email'],
                    $body,
                    //                    vsprintf("%s, this is your new credentials on <a href='%s\'>P-Fleet</a>:<br/> Login: %s<br/> Password: %s", $target),
                    'P-Fleet Login Information'
                );
            }
        }

        return $this;
    }

    public function getContactEmails($checkCarrier = true)
    {
        $carrier = Application_Model_Entity_Entity_Carrier::staticLoad($this->getId());
        if ($checkCarrier && $carrier->getId() && $carrier->getCreateContractorType(
        ) == Application_Model_Entity_Entity_Carrier::MANUALLY_CREATE_CONTRACTOR_USER) {
            return [];
        }
        /**
         * Get all emails from contractor's contact
         */
        $emails = array_unique(
            (new Application_Model_Entity_Entity_Contact_Info())->getCollection()->addFilter(
                $this->getLoadBy(),
                $this->getData($this->getLoadBy())
            )->addFilter('contact_type', Application_Model_Entity_Entity_Contact_Type::TYPE_EMAIL)->getField(
                'value'
            )
        );
        if ($emails) {
            $users = (new User())->getCollection()->addFilter(
                'email',
                $emails,
                'IN-STRING'
            )->addNonDeletedFilter()->getItems();

            if ($users) {
                foreach ($users as $user) {
                    $email = $user->getEmail();
                    if (($key = array_search($email, $emails)) > -1) {
                        if ($user->isContractor()) {
                            $entities = (new Application_Model_Entity_Accounts_UserEntity())->getCollection(
                            )->addFilter('user_id', $user->getId())->addFilter(
                                'entity_id',
                                $this->getEntityId()
                            )->getItems();
                            if (!count($entities)) {
                                $this->addMessage(
                                    [
                                        '<tr><td>' . $this->getCode() . '</td><td>' . $this->getCompanyName(
                                        ) . '</td><td>' . $this->getFirstName() . '</td><td>' . $this->getLastName(
                                        ) . '</td><td>' . $email . '</td></tr>',
                                    ]
                                );
                            }
                        } else {
                            $this->addMessage(
                                [
                                    '<tr><td>' . $user->getId() . '</td><td>' . $user->getCompany(
                                    ) . '</td><td>' . $user->getFirstName() . '</td><td>' . $user->getLastName(
                                    ) . '</td><td>' . $user->getEmail() . '</td></tr>',
                                ]
                            );
                        }
                        unset($emails[$key]);
                    }
                }
            }
            /**
             * Find and relate existing accounts
             */
            /*$users = (new Application_Model_Entity_Accounts_User())
                ->getCollection()
                ->addFilter('email', $emails, 'IN-STRING')
                ->addNonDeletedFilter()
                ->getItems();

            if ($users) {
                $this->setHeaderMessageTemplate(array('The following user accounts were not created because a user account with an identical email already exists.'));
                foreach ($users as $user) {
                    $email = $user->getEmail();
                    if ($user->getAssociatedCarrierId() == $this->getCarrierId()
                        && $user->getEntityId() != $this->getEntityId()
                        && $user->isContractor()
//                        && Application_Model_Entity_Accounts_User::getCurrentUser()->getId() != $user->getId()
                    ) {
                        $user->addData([
                            'entity_id' => $this->getEntityId(),
                            'role_id' => Application_Model_Entity_System_UserRoles::CONTRACTOR_ROLE_ID
                        ])->save();
                    }
                    if (($key = array_search($email, $emails)) > -1) {
                        unset($emails[$key]);
                    }
                }
            }*/
        }

        return $emails;
    }

    public function decrypt($fields = [])
    {
        if (!$fields) {
            $fields = ['tax_id'];
        }

        return parent::decrypt($fields);
    }

    public function updateMasterTemplatePriority()
    {
        $deductionSetup = new Application_Model_Entity_Deductions_Setup();
        $deductionSetup->setLevelId(Application_Model_Entity_System_SetupLevels::MASTER_LEVEL_ID);
        // $deductionSetup->reorderPriority();
    }

    public function updateIndividualTemplatePriority()
    {
        $deductionSetup = new Application_Model_Entity_Deductions_Setup();
        $deductionSetup->setLevelId(Application_Model_Entity_System_SetupLevels::INDIVIDUAL_LEVEL_ID);
        $deductionSetup->setContractorId($this->getEntityId());
        // $deductionSetup->reorderPriority();
    }

    /**
     * @param null $vendorStatus
     * @return Application_Model_Entity_Collection_Entity_ContractorVendor
     */
    public function getVendorsCollection($vendorStatus = null)
    {
        /** @var Application_Model_Entity_Collection_Entity_ContractorVendor $contractorVendorCollection */
        $contractorVendorCollection = (new ContractorVendor())->getCollection();
        $contractorVendorCollection->filterByContractor($this->getEntityId());
        if (!is_null($vendorStatus)) {
            $contractorVendorCollection->statusFilter($vendorStatus);
        }

        return $contractorVendorCollection;
    }

    /**
     * @return array
     */
    public function getActiveVendorIds()
    {
        $vendorIds = $this->getVendorsCollection(VendorStatus::STATUS_ACTIVE)->getField('vendor_id');
        if ($this->getCarrierStatusId() == VendorStatus::STATUS_ACTIVE) {
            $vendorIds[] = $this->getCarrierId();
        }

        return $vendorIds;
    }

    /**
     * update contractor status and set date
     *
     * @return $this
     */
    protected function updateStatus()
    {
        if ($this->status) {
            $this->setData('status', $this->status);
            $date = (new DateTime())->format('Y-m-d');

            if ($this->status == ContractorStatus::STATUS_ACTIVE) {
                if ($this->getData('termination_date')) {
                    $this->setData('rehire_date', $date);
                } else {
                    $this->setData('start_date', $date);
                }
            }
            if ($this->status == ContractorStatus::STATUS_TERMINATED) {
                $this->setData('termination_date', $date);
            }
        }

        return $this;
    }

    public function getAttachments(): array
    {
        return (new Application_Model_Entity_File())
            ->getCollection()
            ->addFilter('entity_id', $this->getEntity()->getId())
            ->addNonDeletedFilter()
            ->getItems();
    }

    public function getPowerunits(?int $contractorId = null): array
    {
        $contractorId = $this->getEntityId() ?: $contractorId;

        return (new Application_Model_Entity_Powerunit_Powerunit())
            ->getCollection()
            ->addFilter('contractor_id', $contractorId)
            ->addNonDeletedFilter()
            ->getItems();
    }

    /**
     * Get a current division active contractors ordered by contractor code
     *
     * @param array $columns
     * @return array|null
     */
    public function getAllContractorsOrderedByCode($columns = ['entity_id', 'company_name', 'code'])
    {
        $db = $this->getResource()->getAdapter();
        $select = $db->select()
            ->from(['contractor' => $this->_name], $columns)
            ->join(['entity' => 'entity'], 'contractor.entity_id = entity.id', [])
            ->where('contractor.status = ?', ContractorStatus::STATUS_ACTIVE)
            ->where('contractor.carrier_id = ?', (string) User::getCurrentUser()->getSelectedCarrier()->getEntityId())
            ->where('entity.deleted = ?', (string) SystemValues::NOT_DELETED_STATUS);
        $contractors = $db->fetchAll($select);
        $result = [];
        foreach ($contractors as $contractor) {
            $result[$contractor['code']] = $contractor;
        }

        return $result;
    }

    /**
     * Get a contractor by contractor code
     *
     * @param string $contractorCode
     * @return array|null
     */
    public function getContractorByCode($contractorCode, $columns = ['entity_id', 'company_name', 'code'])
    {
        $db = $this->getResource()->getAdapter();
        $select = $db->select()
            ->from($this->_name, $columns)
            ->where('code = ?', $contractorCode);
        $contractor = $db->fetchRow($select);

        return $contractor ?: null;
    }
}
