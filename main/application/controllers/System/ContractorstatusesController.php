<?php

include_once "BaseController.php";

class System_ContractorstatusesController extends System_BaseController
{
    protected $_entity;
    protected $_title = 'Contractor statuses';

    public function init()
    {
        $this->_entity = new Application_Model_Entity_System_ContractorStatus();
        parent::init();
    }
}
