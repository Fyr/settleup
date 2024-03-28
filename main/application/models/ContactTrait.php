<?php

trait Application_Model_ContactTrait
{
    public function getContacts($type)
    {
        $contacts = [];
        if ($this->getId()) {
            $contacts = (new Application_Model_Entity_Entity_Contact_Info())->getCollection()->addFilter(
                $this->getLoadBy(),
                $this->getData($this->getLoadBy())
            )->addFilter('contact_type', $type)->getItems();
        }
        if (!count($contacts)) {
            switch ($type) {
                case Application_Model_Entity_Entity_Contact_Type::TYPE_ADDRESS:
                    $contacts = [
                        (new Application_Model_Entity_Entity_Contact_Info())->setData([
                            $this->getLoadBy() => $this->getData($this->getLoadBy()),
                            'contact_type' => $type,
                            'value' => '{"address": "", "address2": "", "city": "", "state": "", "zip": "", "country_code": ""}',
                            'title' => 'Address',
                        ]),
                    ];
                    break;
                case Application_Model_Entity_Entity_Contact_Type::TYPE_HOME_PHONE:
                    $contacts = [
                        (new Application_Model_Entity_Entity_Contact_Info())->setData([
                            $this->getLoadBy() => $this->getData($this->getLoadBy()),
                            'contact_type' => $type,
                            'value' => '',
                            'title' => 'Phone',
                        ]),
                    ];
                    break;
                case Application_Model_Entity_Entity_Contact_Type::TYPE_FAX:
                    $contacts = [
                        (new Application_Model_Entity_Entity_Contact_Info())->setData([
                            $this->getLoadBy() => $this->getData($this->getLoadBy()),
                            'contact_type' => $type,
                            'value' => '',
                            'title' => 'Fax',
                        ]),
                    ];
                    break;
                case Application_Model_Entity_Entity_Contact_Type::TYPE_EMAIL:
                    $contacts = [
                        (new Application_Model_Entity_Entity_Contact_Info())->setData([
                            $this->getLoadBy() => $this->getData($this->getLoadBy()),
                            'contact_type' => $type,
                            'value' => '',
                            'title' => 'Email',
                        ]),
                    ];
                    break;
            }
        }

        return $contacts;
    }

    public function getAllContacts()
    {
        return array_merge(
            $this->getContacts(Application_Model_Entity_Entity_Contact_Type::TYPE_ADDRESS),
            $this->getContacts(Application_Model_Entity_Entity_Contact_Type::TYPE_HOME_PHONE),
            $this->getContacts(Application_Model_Entity_Entity_Contact_Type::TYPE_FAX),
            $this->getContacts(Application_Model_Entity_Entity_Contact_Type::TYPE_EMAIL)
        );
    }

    public function getContractorContacts(): array
    {
        return array_merge(
            $this->getContacts(Application_Model_Entity_Entity_Contact_Type::TYPE_ADDRESS),
            $this->getContacts(Application_Model_Entity_Entity_Contact_Type::TYPE_HOME_PHONE),
            $this->getContacts(Application_Model_Entity_Entity_Contact_Type::TYPE_EMAIL)
        );
    }

    public function getAddress()
    {
        if (($addressArray = $this->getData('address')) === null) {
            $contacts = (new Application_Model_Entity_Entity_Contact_Info())->getCollection()->addFilter(
                $this->getLoadBy(),
                $this->getData($this->getLoadBy())
            )->addFilter('contact_type', Application_Model_Entity_Entity_Contact_Type::TYPE_ADDRESS)->getItems();
            foreach ($contacts as $contact) {
                $address = json_decode($contact->getValue(), true, 512, JSON_THROW_ON_ERROR);
                if (is_array($address) && $address) {
                    if (!isset($address['address']) || $address['address'] === '') {
                        $address['address'] = '-';
                    }
                    if (!isset($address['address2']) || $address['address2'] === '') {
                        $address['address2'] = '-';
                    }
                    if (!isset($address['city']) || $address['city'] === '') {
                        $address['city'] = '-';
                    }
                    if (!isset($address['state']) || $address['state'] === '') {
                        $address['state'] = '-';
                    }
                    if (!isset($address['zip']) || $address['zip'] === '') {
                        $address['zip'] = '-';
                    }
                    if (!isset($address['country_code']) || $address['country_code'] === '') {
                        $address['country_code'] = '-';
                    }

                    $addressArray[] = $address;
                }
            }
        }

        if (empty($addressArray)) {
            $addressArray[] = ['address' => '-', 'address2' => '-', 'city' => '-', 'state' => '-', 'zip' => '-', 'country_code' => '-'];
        }

        return $addressArray;
    }

    public function getEmailToSend()
    {
        $emails = [];

        $contacts = (new Application_Model_Entity_Entity_Contact_Info())->getCollection()->addFilter(
            $this->getLoadBy(),
            $this->getData($this->getLoadBy())
        )->addFilter('contact_type', Application_Model_Entity_Entity_Contact_Type::TYPE_EMAIL)->getItems();
        foreach ($contacts as $contact) {
            if ($contact->getValue()) {
                $emails[$contact->getValue()] = $this->getName();
            }
        }

        return $emails;
    }

    public function getLoadBy()
    {
        return 'entity_id';
    }
}
