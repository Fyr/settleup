<?php

include_once "BaseController.php";

class System_SystemvaluesController extends System_BaseController
{
    protected $_entity;
    protected $_title = 'System values ';
    protected $_form;

    public function init()
    {
        parent::init();
        $this->_form = new Application_Form_System_SystemValues();
        $this->_entity = new Application_Model_Entity_System_SystemValues();
    }
}
