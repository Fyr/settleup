<?php

include_once "BaseController.php";

class System_SetuplevelsController extends System_BaseController
{
    protected $_entity;
    protected $_title = 'Setup levels';

    public function init()
    {
        $this->_entity = new Application_Model_Entity_System_SetupLevels();
        parent::init();
    }
}
