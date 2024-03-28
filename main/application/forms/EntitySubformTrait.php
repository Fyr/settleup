<?php

use Application_Model_Entity_Accounts_User as User;

trait Application_Form_EntitySubformTrait
{
    protected $_entityCount = 0;

    /**
     * update collection of entities for user (user_entity table)
     *
     * @return $this
     */
    public function saveEntities(User $user)
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

        //get entity id from db
        $oldEntityIds = $user->getAssociatedEntityCollection()->getField('entity_id');

        $insertItems = array_diff($newEntityIds, $oldEntityIds);
        $deleteItems = array_diff($oldEntityIds, $newEntityIds);

        //insert new items
        foreach ($insertItems as $item) {
            $userEntity = new Application_Model_Entity_Accounts_UserEntity();
            $userEntity->setUserId($user->getId());
            $userEntity->setEntityId($item);
            $userEntity->save();
        }

        //delete old items
        foreach ($deleteItems as $item) {
            $userEntity = Application_Model_Entity_Accounts_UserEntity::staticLoad([
                'user_id' => $user->getId(),
                'entity_id' => $item,
            ]);
            $userEntity->delete();
        }

        //update current user
        if (!in_array($user->getEntityId(), $newEntityIds)) {
            $firstEntityId = array_pop($newEntityIds);
            if ($firstEntityId) {
                $user->setEntityId($firstEntityId);
                $user->save();
            }
        }

        return $this;
    }

    /**
     * @param $dataHolders
     */
    public function appendEntities($dataHolders, $isRequired = false)
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
                $subform->entity_id_title->setRequired(true);
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
        ) == Application_Model_Entity_System_UserRoles::VENDOR_ROLE_ID) || (!User::getCurrentUser(
        )->hasPermission(
            Application_Model_Entity_Entity_Permissions::CONTRACTOR_USER_CREATE
        ) && $this->getElement('role_id')->getValue(
        ) == Application_Model_Entity_System_UserRoles::CONTRACTOR_ROLE_ID) || ($this->getElement(
            'id'
        ) && $this->getElement('id')->getAttrib('readonly') == 'readonly') || ($user->isContractor(
        ) || $user->isVendor())) {
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
