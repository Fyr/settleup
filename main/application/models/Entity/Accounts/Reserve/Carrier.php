<?php

class Application_Model_Entity_Accounts_Reserve_Carrier extends Application_Model_Base_Entity
{
    /**
     * @return Application_Model_Entity_Accounts_Reserve_Carrier
     */
    public function save()
    {
        $model = new Application_Model_Entity_Accounts_Reserve();
        $model->setData(
            array_merge(
                $this->getData(),
                ['id' => $this->getReserveAccountId()]
            )
        );
        $this->setReserveAccountId($model->save()->getId());
        parent::save();

        return $this;
    }

    public function load($id, $field = null)
    {
        $carrierData = parent::load($id, $field)->getData();
        $basicModel = new Application_Model_Entity_Accounts_Reserve();
        $basicData = $basicModel->load(
            $carrierData['reserve_account_id']
        )->getData();
        $this->setData(array_merge($basicData, $carrierData));

        return $this;
    }

    /**
     * @return Application_Model_Entity_Accounts_Reserve_Carrier
     */
    public function delete()
    {
        parent::delete();
        $model = new Application_Model_Entity_Accounts_Reserve();
        $model->load($this->getReserveAccountId());
        $model->delete();

        return $this;
    }

    /**
     * Adds to the dataset of model related titles for the form
     *
     * @return Application_Model_Entity_Accounts_Reserve_Vendor
     */
    public function getDefaultValues()
    {
        $this->setEntityIdTitle($this->_getEntityIdTitle());

        return $this;
    }

    /**
     * Returns title by entity_id
     *
     * @return array
     */
    protected function _getEntityIdTitle()
    {
        $carrierEntity = new Application_Model_Entity_Entity_Carrier();
        ;
        $carrierEntity->load($this->getEntityId());

        return $carrierEntity->getData($carrierEntity->getTitleColumn());
    }

    /**
     * @param $priorityArr array
     */
    public function setPriority($priorityArr)
    {
        foreach ($priorityArr as $priority => $id) {
            $this->load($id);
            $this->addData(
                [
                    'priority' => $priority,
                ]
            );
            $this->save();
        }
    }

    public function getCarrierCollection()
    {
        $userEntity = Application_Model_Entity_Accounts_User::getCurrentUser();
        $carrierEntity = new Application_Model_Entity_Entity_Carrier();
        $carrierCollection = $carrierEntity->getCollection();

        if ($userEntity->getRoleId() == Application_Model_Entity_System_UserRoles::MANAGER_ROLE_ID) {
            $carrierCollection->addFilter(
                'entity_id',
                Application_Model_Entity_Entity::getCurrentEntity()->getId()
            );
        } else {
            $carrierCollection->addVisibilityFilterForUser();
        }

        return $carrierCollection;
    }
}
