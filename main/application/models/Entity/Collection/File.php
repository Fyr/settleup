<?php

class Application_Model_Entity_Collection_File extends Application_Model_Base_Collection
{
    /**
     * Filters files collection by currently selected carrier
     *
     * @return Application_Model_Entity_Collection_File
     */
    public function addCarrierFilter()
    {
        $this->addFilter(
            'uploaded_by',
            Application_Model_Entity_Accounts_User::getCurrentUser()->getEntity()->getEntityId()
        );

        return $this;
    }
}
