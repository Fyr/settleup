<?php

trait Application_Form_ContactSubformTrait
{
    protected $_subformCount = [];

    public function saveSubforms($entityId = null, $field = 'entity_id')
    {
        $emails = [];
        foreach ($this->getSubForms() as $name => $subform) {
            if (preg_match('/^subform-\S*/', (string) $name)) {
                $contact = new Application_Model_Entity_Entity_Contact_Info();

                $subData = array_pop($subform->getValues()['contacts']);//TODO: NEED a refactoring!

                $contact->setData($subData);
                if ($contact->getDeleted() != Application_Model_Entity_System_SystemValues::DELETED_STATUS) {
                    if ($subData['contact_type'] == Application_Model_Entity_Entity_Contact_Type::TYPE_EMAIL) {
                        if (in_array($subData['value'], $emails)) {
                            continue;
                        } else {
                            $emails[] = $subData['value'];
                        }
                    }
                    if (!$contact->getData($field)) {
                        $contact->setData($field, $entityId);
                    }
                    if (!$contact->getEntityId()) {
                        $contact->unsEntityId();
                    }
                    if (!$contact->getUserId()) {
                        $contact->unsUserId();
                    }
                    $contact->save();
                } else {
                    $contact->delete();
                }
            }
        }
    }

    /**
     * @param $dataHolders
     */
    public function appendSubforms($dataHolders)
    {
        foreach ($dataHolders as $key => $dataHolder) {
            if (is_array($dataHolder)) {
                $dataHolder = (new Application_Model_Entity_Entity_Contact_Info())->setData($dataHolder);
                $fakeId = $key;
            } else {
                $fakeId = $key + 1000;
            }
            if (!$id = $dataHolder->getId()) {
                $id = 'fake-' . $fakeId;
            } else {
                $fakeId = false;
            }
            $this->addSubForm($this->getSubform($dataHolder, $fakeId), 'subform-' . $id);
        }
    }

    /**
     * @param $data
     * @param bool $fakeId
     * @return Application_Form_Account_Contact
     */
    public function getSubform($data, $fakeId = false)
    {
        $subform = new Application_Form_Account_Contact();

        $subform->getElement('value')->setLabel($data->getTitle());

        $subform->populate($data->getData());

        switch ($data->getContactType()) {
            case Application_Model_Entity_Entity_Contact_Type::TYPE_EMAIL:
                $subform->value
                    ->addValidator('EmailAddress')
                    ->addFilter('StringToLower')
                    ->setRequired();
                break;
            case Application_Model_Entity_Entity_Contact_Type::TYPE_ZIP:
                $subform->value->addValidator('PostCode');
                break;
            case Application_Model_Entity_Entity_Contact_Type::TYPE_HOME_PHONE:
            case Application_Model_Entity_Entity_Contact_Type::TYPE_FAX:
                $subform->value->addValidator(
                    'Regex',
                    false,
                    [
                        'pattern' => '/\\(\\d{3}\\)\\s\\d{3}-\\d{4}/',
                        'messages' => 'Invalid phone format! Example: (###) ###-####',
                    ]
                );
                $subform->value->setAttrib('class', 'phone');
                break;
        }
        if ($this instanceof Application_Form_Entity_Vendor || $this instanceof Application_Form_Entity_Contractor) {
            if ($this->correspondence_method->getValue() == $data->getContactType()) {
                $subform->value->setRequired(true);
            }
        }

        if ($fakeId) {
            $id = $fakeId;
        } else {
            $id = $data->getId();
        }

        $subform->setIsArray(true)->setElementsBelongTo('contacts[' . $id . ']');

        if ($this->getElement('id') && $this->getElement('id')->getAttrib('readonly') == 'readonly') {
            $subform->value->setAttrib('readonly', 'readonly');
        }

        return $subform;
    }

    /**
     * @param null $type
     * @return mixed
     */
    public function getContactSubForms($type = null)
    {
        $subforms = $this->_subForms;
        if ($type) {
            foreach ($subforms as $name => $subform) {
                if (preg_match('/^subform-\S*/', (string) $name)) {
                    if ($subform->contact_type->getValue() != $type) {
                        unset($subforms[$name]);
                    } else {
                        if ($subform->deleted->getValue() != Application_Model_Entity_System_SystemValues::DELETED_STATUS) {
                            $this->incContactSubFormsCount($type);
                        }
                    }
                } else {
                    unset($subforms[$name]);
                }
            }
        }

        return $subforms;
    }

    public function getContactSubFormsCount($type)
    {
        return $this->_subformCount[$type] ?? 0;
    }

    public function incContactSubFormsCount($type)
    {
        if (isset($this->_subformCount[$type])) {
            ++$this->_subformCount[$type];
        } else {
            $this->_subformCount[$type] = 1;
        }
    }
}
