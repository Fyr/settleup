<?php

include_once "BaseController.php";

class System_UsercontacttypesController extends System_BaseController
{
    protected $_entity;
    protected $_title = 'User contact types';

    public function init()
    {
        $this->_entity = new Application_Model_Entity_Entity_Contact_Type();
        parent::init();
    }
}
