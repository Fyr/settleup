<?php

include_once "BaseController.php";

class System_ReservetransactiontypesController extends System_BaseController
{
    protected $_entity;
    protected $_title = 'Reserve transaction types';

    public function init()
    {
        $this->_entity = new Application_Model_Entity_System_ReserveTransactionTypes();

        parent::init();
    }
}
