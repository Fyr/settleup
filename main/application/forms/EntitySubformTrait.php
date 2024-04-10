<?php

use Application_Model_Entity_Accounts_User as User;

trait Application_Form_EntitySubformTrait
{
    use Application_Model_Entity_EntitySyncTrait;

    protected $_entityCount = 0;

    public function saveEntities(User $user): self
    {
        //get entity id from form
        $newEntityIds = [];
        foreach ($this->getEntitySubForms() as $name => $subform) {
            $subData = array_pop($subform->getValues()['entities']);
            if (!(int)$subData['deleted'] && (int)$subData['entity_id']) {
                $newEntityIds[] = (int)$subData['entity_id'];
            }
        }
        $newEntityIds = array_unique($newEntityIds);
        $this->syncEntities($user, $newEntityIds);

        return $this;
    }

    public function appendEntities(array $dataHolders, bool $isRequired = false, bool $isReadonly = false): void
    {
        foreach ($dataHolders as $key => $dataHolder) {
            if (is_array($dataHolder)) {
                $dataHolder = (new Application_Model_Entity_Accounts_UserEntity())->setData($dataHolder);
                $fakeId = $key;
            } else {
                $fakeId = $key + 1000;
            }
            if (!$id = $dataHolder->getId()) {
                $id = 'fake-' . $fakeId;
            } else {
                $fakeId = false;
            }

            $subform = $this->getEntities($dataHolder, $fakeId);
            if ($isRequired && !$subform->deleted->getValue()) {
                $subform->entity_id_title->setRequired();
            }
            if ($isReadonly) {
                $subform->entity_id_title->setAttrib('readonly', 'readonly');
            }
            $this->addSubForm($subform, 'entity-subform-' . $id);
        }
    }

    /**
     * @param $data
     * @param bool $fakeId
     * @return Application_Model_Entity_Accounts_UserEntity
     */
    public function getEntities($data, $fakeId = false)
    {
        $subform = new Application_Form_Account_UserEntity();

        $subform->populate($data->getData());

        if ($fakeId) {
            $id = $fakeId;
        } else {
            $id = $data->getId();
            $subform->entity_id->setAttrib('readonly', 'readonly');
        }

        $subform->setIsArray(true)->setElementsBelongTo('entities[' . $id . ']');
        $user = User::getCurrentUser();

        if ((!User::getCurrentUser()->hasPermission(
            Application_Model_Entity_Entity_Permissions::VENDOR_USER_CREATE
        ) && $this->getElement('role_id')->getValue(
        ) == Application_Model_Entity_System_UserRoles::ONBOARDING_ROLE_ID) || (!User::getCurrentUser(
        )->hasPermission(
            Application_Model_Entity_Entity_Permissions::CONTRACTOR_USER_CREATE
        ) && $this->getElement('role_id')->getValue(
        ) == Application_Model_Entity_System_UserRoles::SPECIALIST_ROLE_ID) || ($this->getElement(
            'id'
        ) && $this->getElement('id')->getAttrib('readonly') == 'readonly') || ($user->isSpecialist(
        ) || $user->isOnboarding())) {
            $subform->readonly();
        }

        $subform->configure();

        return $subform;
    }

    /**
     * @param null $type
     * @return mixed
     */
    public function getEntitySubForms()
    {
        $subforms = $this->_subForms;
        foreach ($subforms as $name => $subform) {
            if (preg_match('/^entity-subform-\S*/', (string) $name)) {
                if ($subform->deleted->getValue() != Application_Model_Entity_System_SystemValues::DELETED_STATUS) {
                    $this->_entityCount++;
                }
            } else {
                unset($subforms[$name]);
            }
        }

        return $subforms;
    }

    public function getEntitySubFormsCount()
    {
        return $this->_entityCount;
    }
}
