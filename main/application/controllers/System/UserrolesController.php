<?php

include_once "BaseController.php";

class System_UserrolesController extends System_BaseController
{
    protected $_entity;
    protected $_title = 'User roles';

    public function init()
    {
        $this->_entity = new Application_Model_Entity_System_UserRoles();
        parent::init();
    }
}
