<?php

include_once "BaseController.php";

class System_CycleperiodsController extends System_BaseController
{
    protected $_entity;
    protected $_title = 'Cycle periods';

    public function init()
    {
        $this->_entity = new Application_Model_Entity_System_CyclePeriod();
        parent::init();
    }
}
