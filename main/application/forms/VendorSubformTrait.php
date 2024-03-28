<?php

trait Application_Form_VendorSubformTrait
{
    protected $_vendorCount = 0;

    public function saveVendors($entityId = null)
    {
        $saved = [];
        foreach ($this->getVendorSubForms() as $name => $subform) {
            if (preg_match('/^vendor-subform-\S*/', (string) $name)) {
                $contractorVendor = new Application_Model_Entity_Entity_ContractorVendor();

                $subData = array_pop($subform->getValues()['vendor']);//TODO: NEED a refactoring!

                $contractorVendor->setData($subData);
                if ((int)$contractorVendor->getVendorId() || $contractorVendor->getId()) {
                    if ($contractorVendor->getDeleted(
                    ) != Application_Model_Entity_System_SystemValues::DELETED_STATUS) {
                        if (!$contractorVendor->getContractorId()) {
                            $contractorVendor->setContractorId($entityId);
                        }
                        if (!$contractorVendor->getVendorAcct()) {
                            $contractorVendor->setData('vendor_acct', null);
                        }
                        if (!$contractorVendor->getId()) {
                            $existingEntity = Application_Model_Entity_Entity_ContractorVendor::staticLoad([
                                'contractor_id' => $contractorVendor->getContractorId(),
                                'vendor_id' => $contractorVendor->getVendorId(),
                            ]);
                            if ($existingEntity->getId()) {
                                $contractorVendor->setId($existingEntity->getId());
                            }
                        }
                        $contractorVendor->save();
                        $saved[$contractorVendor->getId()] = $contractorVendor;
                    } else {
                        if (!isset($saved[$contractorVendor->getId()])) {
                            $contractorVendor->setContractorId($this->entity_id->getValue())->delete();
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $dataHolders
     */
    public function appendVendors($dataHolders)
    {
        foreach ($dataHolders as $key => $dataHolder) {
            if (is_array($dataHolder)) {
                $dataHolder = (new Application_Model_Entity_Entity_ContractorVendor())->setData($dataHolder);
                $fakeId = $key;
            } else {
                $fakeId = $key + 1000;
            }
            if (!$id = $dataHolder->getId()) {
                $id = 'fake-' . $fakeId;
            } else {
                $fakeId = false;
            }
            $this->addSubForm($this->getVendors($dataHolder, $fakeId), 'vendor-subform-' . $id);
        }
    }

    /**
     * @param $data
     * @param bool $fakeId
     * @return Application_Form_Entity_ContractorVendor
     */
    public function getVendors($data, $fakeId = false)
    {
        $subform = new Application_Form_Entity_ContractorVendor();

        $subform->populate($data->getData());

        if ($fakeId) {
            $id = $fakeId;
        } else {
            $id = $data->getId();
            $subform->vendor_id->setAttrib('readonly', 'readonly');
        }

        $subform->setIsArray(true)->setElementsBelongTo('vendor[' . $id . ']');

        if (!Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
            Application_Model_Entity_Entity_Permissions::CONTRACTOR_VENDOR_AUTH_MANAGE
        ) || ($this->getElement('id') && $this->getElement('id')->getAttrib('readonly') == 'readonly')) {
            $subform->readonly();
        }

        return $subform;
    }

    /**
     * @param null $type
     * @return mixed
     */
    public function getVendorSubForms()
    {
        $subforms = $this->_subForms;
        foreach ($subforms as $name => $subform) {
            if (preg_match('/^vendor-subform-\S*/', (string) $name)) {
                if ($subform->deleted->getValue() != Application_Model_Entity_System_SystemValues::DELETED_STATUS) {
                    $this->_vendorCount++;
                }
            } else {
                unset($subforms[$name]);
            }
        }

        return $subforms;
    }

    public function getVendorSubFormsCount()
    {
        return $this->_vendorCount;
    }
}
