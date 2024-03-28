<?php

include_once "BaseController.php";

class System_CycletypesController extends System_BaseController
{
    protected $_entity;
    protected $_title = 'Cycle types';

    public function init()
    {
        $this->_entity = new Application_Model_Entity_System_CycleTypes();
        parent::init();
    }
}
