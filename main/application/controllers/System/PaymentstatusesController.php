<?php

include_once "BaseController.php";

class System_PaymentstatusesController extends System_BaseController
{
    protected $_entity;
    protected $_title = 'Compensation statuses';

    public function init()
    {
        $this->_entity = new Application_Model_Entity_System_PaymentStatus();
        parent::init();
    }
}
