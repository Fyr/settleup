<?php

include_once "BaseController.php";

class System_RecurringtitlesController extends System_BaseController
{
    protected $_entity;
    protected $_title = 'Recurring titles';

    public function init()
    {
        $this->_entity = new Application_Model_Entity_System_RecurringTitle();
        parent::init();
    }
}
