<?php

class Application_Model_Entity_Entity_ContractorVendorTemp extends Application_Model_Base_TempEntity
{
    protected $vendor;
    protected $parentEntity;

    public function _beforeSave(): self
    {
        $this->check();
        parent::_beforeSave();

        return $this;
    }

    public function check(): self
    {
        if (!$this->getId()) {
            $this->setupStatus();
            if (!$this->getVendor()->getId()) {
                $this->addError('Vendor not found (invalid Vendor ID)');
            }
        }

        if ($this->getError()) {
            $this->setStatusId(
                Application_Model_Entity_System_PaymentTempStatus::CONST_STATUS_NOT_VALID
            );
        } else {
            $this->setStatusId(
                Application_Model_Entity_System_PaymentTempStatus::CONST_STATUS_VALID
            );
        }

        return $this;
    }

    public function getControllerName()
    {
        return 'contractors_index';
    }

    public function getVendor()
    {
        if (!isset($this->vendor)) {
            if (!$this->getVendorCode()) {
                $this->vendor = new Application_Model_Entity_Entity_Vendor();
            } else {
                $carrier = Application_Model_Entity_Accounts_User::getCurrentUser()->getSelectedCarrier();
                /*if (strcmp($this->getVendorCode(), $carrier->getId()) === 0) {
                    if ($carrier->getStatus() == Application_Model_Entity_System_SystemValues::CONFIGURED_STATUS) {
                        $this->vendor = $carrier;
                    }
                } else {*/
                $entity = new Application_Model_Entity_Entity_Vendor();
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
                //                }
            }
            $this->setVendorId($this->vendor->getEntityId());
        }

        return $this->vendor;
    }

    public function setupStatus()
    {
        if (mb_strtolower((string) $this->getStatus()) == 'approved') {
            $this->setStatus(Application_Model_Entity_System_VendorStatus::STATUS_ACTIVE);
        } elseif (mb_strtolower((string) $this->getStatus()) == 'rescinded') {
            $this->setStatus(Application_Model_Entity_System_VendorStatus::STATUS_RESCINDED);
        } else {
            $this->setStatus(null);
            $this->addError('Status is not valid (acceptable: Approved/Rescinded)');
        }
    }

    public function approve($entityId)
    {
        $this->parentEntity = $this->getResource()->getParentEntity();
        $this->parentEntity->setData($this->getData());
        $this->parentEntity->unsId();
        $this->parentEntity->setContractorId($entityId);
        $this->parentEntity->save();

        return $this;
    }

    public function getExportCollection($idOrFilters = null)
    {
        $entity = new Application_Model_Entity_Entity_ContractorVendor();
        if ((int)$idOrFilters && !is_array($idOrFilters)) {
            $collection = [$entity->load($idOrFilters)];
        } else {
            $collection = $entity->getCollection()->addNonDeletedFilter();
            if ($this->getContractorId()) {
                $collection->addFilter('contractor_id', $this->getContractorId());
                $this->applyFilters($collection, $idOrFilters);
            }
        }

        return $collection;
    }
}
