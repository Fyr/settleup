<?php

include_once "BaseController.php";

class System_PaymenttypesController extends System_BaseController
{
    protected $_entity;
    protected $_title = 'Compensation types';

    public function init()
    {
        $this->_entity = new Application_Model_Entity_System_PaymentType();
        parent::init();
    }
}
