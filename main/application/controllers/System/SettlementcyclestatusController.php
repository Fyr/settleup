<?php

include_once "BaseController.php";

class System_SettlementcyclestatusController extends System_BaseController
{
    protected $_entity;
    protected $_title = 'Settlement cycle status';

    public function init()
    {
        $this->_entity = new
        Application_Model_Entity_System_SettlementCycleStatus();

        parent::init();
    }
}
