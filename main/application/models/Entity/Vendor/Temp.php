<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Vendor as Vendor;
use Application_Model_Entity_Vendor_Temp as VendorTemp;

/**
 * @method Application_Model_Entity_Collection_Entity_VendorTemp getCollection()
 * @method Application_Model_Resource_Vendor_Temp getResource()
 */
class Application_Model_Entity_Vendor_Temp extends Application_Model_Base_TempEntity
{
    public function _beforeSave(): self
    {
        $this->check();
        parent::_beforeSave();

        return $this;
    }

    public function check(): self
    {
        return $this
            ->setupDivisionId()
            ->setupVendorCode()
            ->setupVendorName()
            ->setupStatusId();
    }

    private function setupDivisionId(): self
    {
        $currentDivision = User::getCurrentUser()->getEntity();
        $this->setCarrierId($currentDivision->getEntityId());
        $divisionCode = $currentDivision->getShortCode();

        if ($this->getDivisionCode()) {
            $division = (new Application_Model_Entity_Entity_Carrier())
                ->getCollection()
                ->addFilter('short_code', $this->getDivisionCode())
                ->getFirstItem();
            if ($division->isEmpty()) {
                $this->addWarning('Division ' . $this->getDivisionCode() . ' not found (will be selected current '
                    . $currentDivision->getName() . ')');
            } else {
                if ($division->getId() !== $currentDivision->getId()) {
                    $this->addWarning('Division is different from the current one');
                }
                $divisionCode = $division->getShortCode();
                $this->setCarrierId($division->getEntityId());
            }
        }

        $this->setDivisionCode($divisionCode);

        return $this;
    }

    private function setupVendorCode(): self
    {
        if (!$this->getCode()) {
            $this->addJsonError(
                'Code is required and can not be empty (invalid Code)',
                $this->colCode()
            );
        }

        if ($this->getCode()) {
            if ($this->isCodeExist()) {
                $this->addJsonError('Code already in use (invalid Code)', $this->colCode());
            }
            if ($this->isTempCodeExist()) {
                $this->addJsonError('Duplicate Code in uploaded data (invalid Code)', $this->colCode());
            }
        }

        return $this;
    }

    private function setupVendorName(): self
    {
        if (!$this->getName()) {
            $this->addJsonError(
                'Vendor Name is required and can not be empty (invalid Vendor Name)',
                $this->colName()
            );
        }

        return $this;
    }

    private function isCodeExist(): bool
    {
        $contractor = (new Vendor())
            ->getCollection()
            ->addFilter('code', $this->getCode())
            ->getFirstItem();

        return !$contractor->isEmpty();
    }

    private function isTempCodeExist(): bool
    {
        $contractorTemp = (new VendorTemp())
            ->getCollection()
            ->addFilter('code', $this->getCode())
            ->addFilter('source_id', $this->getSourceId())
            ->getFirstItem();

        return !$contractorTemp->isEmpty();
    }

    public function getControllerName(): string
    {
        return 'vendors_index';
    }

    public function getExportCollection($idOrFilters = null)
    {
        $entity = new Vendor();
        if ((int) $idOrFilters && !is_array($idOrFilters)) {
            $collection = [$entity->load($idOrFilters)];
        } else {
            $collection = $entity->getCollection();
            $this->applyFilters($collection, $idOrFilters);
        }

        return $collection;
    }
}
