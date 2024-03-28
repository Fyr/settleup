<?php

use Application_Model_Entity_Accounts_Reserve_Vendor as VendorRA;
use Application_Model_Entity_Entity_Contractor as Contractor;
use Application_Model_Entity_Entity_Vendor as Vendor;
use Application_Model_Entity_System_FileTempStatus as FileTempStatus;

class Application_Model_Entity_Accounts_Reserve_ContractorTemp extends Application_Model_Base_TempEntity
{
    public $vendor;
    public $contractor;
    public $vendorRA;

    public function check()
    {
        $error = '';

        if (!$this->getVendor()->getId()) {
            $error .= 'Vendor not found (invalid Vendor ID)<br>';
        }
        if (!$this->getVendorRA()->getId()) {
            $error .= 'Vendor Reserve Account code not found<br>';
        } else {
            $this->setReserveAccountVendorId($this->vendorRA->getId());
        }
        if (!$this->getContractor()->getId()) {
            $error .= 'Contractor not found (invalid Contractor ID)<br>';
        } else {
            $this->setEntityId($this->contractor->getEntityId());
        }

        if (empty($this->_data['description'])) {
            $error .= 'Description name should not be empty<br>';
        }



        if (strlen($error)) {
            $this->setStatusId(FileTempStatus::CONST_STATUS_NOT_VALID);
            $this->setError($error);
        } else {
            $this->setStatusId(FileTempStatus::CONST_STATUS_VALID);
        }

        return $this;
    }

    public function _beforeSave()
    {
        $this->check();
        parent::_beforeSave();

        return $this;
    }

    public function getControllerName()
    {
        return 'reserve_accountcontractor';
    }

    public function getVendor()
    {
        if (!isset($this->vendor)) {
            $entity = new Vendor();
            $collection = $entity->getCollection();
            $collection->addFilter(
                'code',
                $this->getVendorCode()
            );

            $this->vendor = $collection->getFirstItem();
        }

        return $this->vendor;
    }

    public function getVendorRA()
    {
        if (!isset($this->vendorRA)) {
            $entity = new VendorRA();
            $collection = $entity->getCollection();
            $collection->addFilter(
                'entity_id',
                $this->vendor->getEntityId()
            )
                ->addFilter(
                    'vendor_reserve_code',
                    $this->getVendorReserveCode()
                );

            $this->vendorRA = $collection->getFirstItem();
        }

        return $this->vendorRA;
    }

    public function getContractor()
    {
        if (!isset($this->contractor)) {
            $entity = new Contractor();
            $collection = $entity->getCollection();
            $collection->addFilter(
                'code',
                $this->getContractorCode()
            );

            $this->contractor = $collection->getFirstItem();
        }

        return $this->contractor;
    }

    public function getExportCollection($idOrFilters = null)
    {
        $entity = new Contractor();
        if ((int) $idOrFilters && !is_array($idOrFilters)) {
            $collection = [$entity->load($idOrFilters)];
        } else {
            $collection = $entity->getCollection();
            $this->applyFilters($collection, $idOrFilters);
        }

        return $collection;
    }
}
