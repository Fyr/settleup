<?php

class Application_Model_Entity_Collection_Entity_Contact_Info extends Application_Model_Base_Collection implements
    JsonSerializable
{
    /**
     * @return Application_Model_Entity_Collection_Entity_Contact_Info
     */
    public function _beforeLoad()
    {
        parent::_beforeLoad();

        $this->addFieldsForSelect(
            new Application_Model_Entity_Entity_Contact_Info(),
            'contact_type',
            new Application_Model_Entity_Entity_Contact_Type(),
            'id',
            ['title']
        );

        return $this;
    }

    /**
     * return ZIP of entity
     *
     * @return string|null
     */
    public function getZip()
    {
        $this->addFilter(
            'entity_contact_info.contact_type',
            Application_Model_Entity_Entity_Contact_Type::TYPE_ZIP
        );
        $contactEntity = $this->getFirstItem();
        if ($contactEntity instanceof Application_Model_Entity_Entity_Contact_Info) {
            return $contactEntity->getValue();
        }

        return null;
    }

    /**
     * return state of entity
     *
     * @return string|null
     */
    public function getState()
    {
        $this->addFilter(
            'entity_contact_info.contact_type',
            Application_Model_Entity_Entity_Contact_Type::TYPE_STATE
        );
        $contactEntity = $this->getFirstItem();
        if ($contactEntity instanceof Application_Model_Entity_Entity_Contact_Info) {
            return $contactEntity->getValue();
        }

        return null;
    }

    /**
     * return city of entity
     *
     * @return string|null
     */
    public function getCity()
    {
        $this->addFilter(
            'entity_contact_info.contact_type',
            Application_Model_Entity_Entity_Contact_Type::TYPE_CITY
        );
        $contactEntity = $this->getFirstItem();
        if ($contactEntity instanceof Application_Model_Entity_Entity_Contact_Info) {
            return $contactEntity->getValue();
        }

        return null;
    }

    /**
     * return firstAddress of entity
     *
     * @return string|null
     */
    public function getFirstAddress()
    {
        $this->addFilter(
            'entity_contact_info.contact_type',
            Application_Model_Entity_Entity_Contact_Type::TYPE_ADDRESS
        );
        $contactEntity = $this->getFirstItem();
        if ($contactEntity instanceof Application_Model_Entity_Entity_Contact_Info) {
            return $contactEntity->getValue();
        }

        return null;
    }

    /**
     * return secondAddress of entity
     *
     * @return string|null
     */
    public function getSecondAddress()
    {
        $this->addFilter(
            'entity_contact_info.contact_type',
            Application_Model_Entity_Entity_Contact_Type::TYPE_ADDRESS
        );
        $contactEntity = $this->getItems();
        if (count($contactEntity) >= 2) {
            return next($contactEntity)->getValue();
        }

        return null;
    }

    public function addAddressFilter()
    {
        $this->addFilter('contact_type', Application_Model_Entity_Entity_Contact_Type::TYPE_ADDRESS);

        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize(): mixed
    {
        $data = [];
        foreach ($this->getItems() as $address) {
            $data[] = json_decode((string) $address->getValue(), true, 512, JSON_THROW_ON_ERROR);
        }

        return $data;
    }
}
