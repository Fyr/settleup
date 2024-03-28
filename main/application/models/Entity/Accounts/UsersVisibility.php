<?php

class Application_Model_Entity_Accounts_UsersVisibility extends Application_Model_Base_Entity
{
    public $entities = [];
    public $grids = [];
    public $userEntityId;

    /**
     * Adds
     *
     * @param int $userEntityId Id of selected User
     * @param array of int $selectedItemsId An array of ids of contractors
     *   that have been added from the pop-up grid
     * @return Application_Model_Entity_Accounts_UsersVisibility
     */
    public function addEntities($userEntityId, $selectedItemsId)
    {
        foreach ($selectedItemsId as $itemId) {
            $this->unsId();
            $this->setEntityId($userEntityId);
            $this->setParticipantId($itemId);
            $this->save();
        }

        return $this;
    }

    /**
     * Sets data for view
     *
     * @return Application_Model_Entity_Accounts_UsersVisibility
     */
    public function getDefaultValues()
    {
        $this->_setEntities()->_setGrids();

        return $this;
    }

    /**
     * Sets $this->entities
     *
     * @return Application_Model_Entity_Accounts_UsersVisibility
     */
    protected function _setEntities()
    {
        $entityEntity = new Application_Model_Entity_Entity();
        $userEntity = new Application_Model_Entity_Accounts_User();

        $userId = $entityEntity->load($this->userEntityId, 'id')->getUserId();
        $userEntity->load($userId, 'id');
        switch ($userEntity->getRoleId()) {
            case Application_Model_Entity_System_UserRoles::CARRIER_ROLE_ID:
                $this->entities = [
                    'Contractors' => new Application_Model_Entity_Entity_Contractor(),
                    'Vendors' => new Application_Model_Entity_Entity_Vendor(),
                ];
                break;
            case Application_Model_Entity_System_UserRoles::VENDOR_ROLE_ID:
                $this->entities = [
                    'Carriers' => new Application_Model_Entity_Entity_Carrier(),
                    'Contractors' => new Application_Model_Entity_Entity_Contractor(),
                ];
                break;
        }

        return $this;
    }

    /**
     * Sets $this->grids - settings for popup checkbox with tabs
     *
     * @return Application_Model_Entity_Accounts_UsersVisibility
     */
    protected function _setGrids()
    {
        foreach ($this->entities as $title => $entity) {
            $grid = [
                'tabTitle' => $title,
                'columns' => $entity->getResource()->getInfoFields(),
                'idField' => 'entity_id',
                'items' => $entity->getCollection()->addFilterByAddedEntities($this->userEntityId)->getItems(),
            ];
            array_push($this->grids, $grid);
        }

        return $this;
    }
}
