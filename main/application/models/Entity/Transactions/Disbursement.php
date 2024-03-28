<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;
use Application_Model_Entity_System_PaymentStatus as PaymentStatus;

/**
 * @method $this staticLoad($id, $field = null)
 */
class Application_Model_Entity_Transactions_Disbursement extends Application_Model_Base_Entity
{
    use Application_Model_Entity_SettlementCycleTrait;

    /** @var Application_Model_Base_CryptAdvanced */
    protected $crypt;

    public function __construct()
    {
        parent::__construct();
        $this->crypt = new Application_Model_Base_CryptAdvanced();
    }

    public function _beforeSave()
    {
        parent::_beforeSave();

        if ($this->getCreatedDatetime() == null) {
            $this->setCreatedDatetime(date('Y-m-d H:i:s'));
        }

        if ($this->getCreatedBy() === User::SYSTEM_USER || ($this->getCreatedBy(
        ) == null && $this->getId())) {
            $this->setCreatedBy(null);
        } elseif ($this->getCreatedBy() == null && !$this->getId()) {
            $this->setCreatedBy(
                User::getCurrentUser()->getId()
            );
        }

        if ($this->getStatus() == null) {
            $this->setStatus(
                PaymentStatus::NOT_APPROVED_STATUS
            );
        }

        if ($this->getDeductionId() == '') {
            unset($this->_data['deduction_id']);
        }

        if ($this->getApprovedBy() == '') {
            unset($this->_data['approved_by']);
        }

        if ($this->getApprovedDatetime() == null) {
            $this->unsApprovedDatetime();
        }

        if ($this->getSourceId() == '0') {
            unset($this->_data['source_id']);
        }

        $entity = (new Application_Model_Entity_Entity())->load($this->getEntityId());
        if (!$this->getSenderName()) {
            $this->setSenderName($entity->getEntityName());
        }

        if (!$this->getEntityCode()) {
            $targetEntity = $entity->getEntityByType();
            $this->setEntityCode($targetEntity->getCode() ?: $targetEntity->getId());
        }

        $this->setAmount(str_replace(',', '', (string) $this->getAmount()));

        //        if ($this->getId()) {
        //            $cycle = (new Application_Model_Entity_Settlement_Cycle())->load($this->getSettlementCycleId());
        //            $cycle->setDisbursementStatus(Application_Model_Entity_System_PaymentStatus::NOT_APPROVED_STATUS)->save();
        //        }

        if ($this->getData('reissue_parent_id') === '') {
            $this->unsetData('reissue_parent_id');
        }

        if ($this->getData('disbursement_reference') === '') {
            $this->unsetData('disbursement_reference');
        }

        return $this;
    }

    /**
     * Sets the related titles by id filds
     *
     * @return Application_Model_Entity_Accounts_Reserve_Transaction
     */
    public function getDefaultData()
    {
        $this->setProcessTypeTitle($this->_getProcessTypeTitle());
        $this->setCreatedByTitle($this->_getCreatedByTitle());
        $this->setStatusTitle($this->_getStatusTitle());
        $this->setApprovedDatetime($this->_getApprovedDatetime());
        $this->setApprovedByTitle($this->_getApprovedByTitle());

        return $this;
    }

    /**
     * Returns string title by 'process_type' field
     *
     * @return string
     */
    private function _getProcessTypeTitle()
    {
        $typeEntity = new Application_Model_Entity_System_DisbursementTransactionTypes();

        return $typeEntity->load($this->getProcessType())->getTitle();
    }

    /**
     * Returns title by 'created_by' field
     *
     * @return string
     */
    private function _getCreatedByTitle()
    {
        $userEntity = new User();
        if ($userId = $this->getCreatedBy()) {
            return $userEntity->load($userId)->getName();
        } else {
            return "System";
        }
    }

    /**
     * Returns title by 'status' field
     *
     * @return string
     */
    private function _getStatusTitle()
    {
        //        $typeEntity = new Application_Model_Entity_System_PaymentStatus();
        //        return $typeEntity->load($this->getStatus())->getTitle();
        if ($this->getStatus() == PaymentStatus::REVERSED_STATUS) {
            return PaymentStatus::staticLoad(PaymentStatus::REVERSED_STATUS)->getTitle();
        }

        return $this->getCycle()->getDisbursementStatusTitle();
    }

    /**
     * Returns title by 'approved_by' field
     *
     * @return string
     */
    private function _getApprovedByTitle()
    {
        $userEntity = new User();

        return $userEntity->load($this->getCycle()->getDisbursementApprovedBy())->getName();
    }

    /**
     * Returns title by 'approved_by' field
     *
     * @return string
     */
    protected function _getApprovedDatetime()
    {
        return $this->getCycle()->getDisbursementApprovedDatetime();
    }

    /**
     * @return Application_Model_Entity_Entity
     */
    public function getEntity()
    {
        $entity = new Application_Model_Entity_Entity();
        $entity->load($this->getEntityId());

        return $entity;
    }

    /**
     * @return Application_Model_Entity_System_DisbursementTransactionTypes
     */
    public function getType()
    {
        $entity = new Application_Model_Entity_System_DisbursementTransactionTypes();
        $entity->load($this->getProcessType());

        return $entity;
    }

    /**
     * @return Application_Model_Entity_Settlement_Cycle
     */
    public function getCycle()
    {
        return $this->getSettlementCycle();
    }

    /**
     * @return bool
     */
    public function checkPermissions()
    {
        $user = User::getCurrentUser();
        if ($entityId = $user->getCarrierEntityId()) {
            if ($this->getCycle()->getCarrierId() == $entityId) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isAllowReissue()
    {
        $user = User::getCurrentUser();
        if (($user->isCarrier() && $user->hasPermission(Permissions::DISBURSEMENT_REISSUE)) || $user->isAdmin()) {
            if ($this->getEntity()->isContractor()) {
                if ($this->getCycle()->getDisbursementStatus() == PaymentStatus::APPROVED_STATUS && $this->getStatus(
                ) != PaymentStatus::REVERSED_STATUS) {
                    return true;
                }
            }
        }

        return false;
    }

    public function updateParentDisbursement()
    {
        if ($this->getReissueParentId()) {
            $disbursement = new self();
            $disbursement->load($this->getReissueParentId());
            $disbursement->setStatus(PaymentStatus::REVERSED_STATUS);
            $disbursement->setAmount(0);
            $disbursement->save();
        }
    }

    public function getMICRCode()
    {
        $code = '';
        $code .= 'c'; //"On-US"
        $code .= $this->getDisbursementReference();
        $code .= 'c';//"On-US"
        $code .= " ";
        $code .= 'a';//"Transit"
        $code .= 'a';//"Transit"
        $code .= " ";
        $code .= 'c';//"On-US"

        return $code;
    }
}
