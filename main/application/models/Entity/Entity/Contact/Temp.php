<?php

class Application_Model_Entity_Entity_Contact_Temp extends Application_Model_Base_TempEntity
{
    protected $parentEntity;

    public function _beforeSave(): self
    {
        if (!$this->getSkipCheck()) {
            $this->setDataByType();
            $this->check();
        }
        parent::_beforeSave();

        return $this;
    }

    public function check(): self
    {
        if (!$this->getId()) {
            if (!$this->getContactType() || $this->getValue() === null) {
                $this->addJsonError('Contractor Contact information is incorrect', $this->colContactType());
            } else {
                $this->checkValue();
            }
        }

        return $this->setupStatusId();
    }

    public function getControllerName()
    {
        return 'contractors_index';
    }

    public function setDataByType()
    {
        if ($this->getAddress() !== null) {
            $this->setContactType(Application_Model_Entity_Entity_Contact_Type::TYPE_ADDRESS);
            $this->setValue(
                json_encode(
                    [
                        'address' => $this->getAddress(),
                        'address2' => $this->getAddress2(),
                        'city' => $this->getCity(),
                        'state' => $this->getState(),
                        'zip' => $this->getZip(),
                        'country_code' => $this->getCountryCode(),
                    ],
                    JSON_THROW_ON_ERROR
                )
            );
        } elseif ($this->getPhone() !== null) {
            $this->setContactType(Application_Model_Entity_Entity_Contact_Type::TYPE_HOME_PHONE);
            $this->setValue($this->getPhone());
        } elseif ($this->getFax() !== null) {
            $this->setContactType(Application_Model_Entity_Entity_Contact_Type::TYPE_FAX);
            $this->setValue($this->getFax());
        } elseif ($this->getEmail() !== null) {
            $this->setContactType(Application_Model_Entity_Entity_Contact_Type::TYPE_EMAIL);
            $this->setValue($this->getEmail());
        }

        return $this;
    }

    public function checkValue()
    {
        if ($this->getValue() !== null && $this->getContactType()) {
            switch ($this->getContactType()) {
                case Application_Model_Entity_Entity_Contact_Type::TYPE_HOME_PHONE:
                    if (strlen((string)$this->getValue()) == 10 && filter_var(
                        $this->getValue(),
                        FILTER_VALIDATE_FLOAT
                    )) {
                        $this->setValue(vsprintf("(%d) %d-%d", sscanf($this->getValue(), '%3d%3d%4d')));
                    } elseif (!preg_match('/\\(\\d{3}\\)\\s\\d{3}-\\d{4}/', (string)$this->getValue())) {
                        $this->addJsonError('Phone format is invalid (example: (123) 456-7890)', 'Phone');
                    }
                    break;
                case Application_Model_Entity_Entity_Contact_Type::TYPE_FAX:
                    if (strlen((string)$this->getValue()) == 10 && filter_var(
                        $this->getValue(),
                        FILTER_VALIDATE_FLOAT
                    )) {
                        $this->setValue(vsprintf("(%d) %d-%d", sscanf($this->getValue(), '%3d%3d%4d')));
                    } elseif (!preg_match('/\\(\\d{3}\\)\\s\\d{3}-\\d{4}/', (string)$this->getValue())) {
                        $this->addJsonError('Fax format is invalid (example: (123) 456-7890)', 'Fax');
                    }
                    break;
                case Application_Model_Entity_Entity_Contact_Type::TYPE_EMAIL:
                    if (!filter_var($this->getValue(), FILTER_VALIDATE_EMAIL)) {
                        $this->addJsonError('Email format is invalid (example: username@example.com)', 'Email');
                    }
                    break;
                case Application_Model_Entity_Entity_Contact_Type::TYPE_ADDRESS:
                    if ($this->getCountryCode()) {
                        if (!in_array(mb_strtolower((string) $this->getCountryCode()), ['us', 'can', 'mex'])) {
                            $this->addJsonError('Country Code is invalid (acceptable: US/CAN/MEX)', 'country_code');
                        }
                    }
                    if (in_array(strlen((string)$this->getZip()), [5, 9])
                        && filter_var($this->getZip(), FILTER_VALIDATE_FLOAT)
                    ) {
                        $zipParts = sscanf($this->getZip(), '%5d%4d');
                        if ($zipParts[1]) {
                            $this->setZip(implode('-', $zipParts));
                        } else {
                            $this->setZip($zipParts[0]);
                        }
                        $this->setJSONFromAddress();
                    } elseif ($this->getZip() && !preg_match('/^\d{5}([\-]?\d{4})?$/', (string)$this->getZip())) {
                        $this->addJsonError('Zip format is invalid (example: 12345-6789 or 12345)', 'zip');
                    }
                    break;
            }
        }

        return $this;
    }

    public function approve($entityId)
    {
        $this->parentEntity = $this->getResource()->getParentEntity();
        $this->parentEntity->setData($this->getData());
        $this->parentEntity->unsId();
        $this->parentEntity->setEntityId($entityId);
        $this->parentEntity->save();

        return $this;
    }

    public function setAddressFormJSON($addressJSON)
    {
        if ($addressData = json_decode((string)$addressJSON, true, 512, JSON_THROW_ON_ERROR)) {
            foreach (['address', 'address2', 'city', 'state', 'zip', 'country_code'] as $partName) {
                $value = $addressData[$partName] ?? null;
                $this->setData($partName, $value);
            }
        }

        return $this;
    }

    public function setJSONFromAddress()
    {
        foreach (['address', 'address2', 'city', 'state', 'zip', 'country_code'] as $partName) {
            $value = $this->getData($partName);
            $data[$partName] = $value ?? '';
        }
        $this->setValue(json_encode($data, JSON_THROW_ON_ERROR));

        return $this;
    }

    public function getExportCollection($idOrFilters = null)
    {
        $entity = new Application_Model_Entity_Entity_Contact_Info();
        if ((int)$idOrFilters && !is_array($idOrFilters)) {
            $collection = [$entity->load($idOrFilters)];
        } else {
            $collection = $entity->getCollection()->addNonDeletedFilter();
            if ($this->getContractorId()) {
                $collection->addFilter('entity_id', $this->getContractorId());
                $this->applyFilters($collection, $idOrFilters);
            }
        }

        return $collection;
    }
}
