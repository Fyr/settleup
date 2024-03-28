<?php

class System_EmaillogController extends Zend_Controller_Action
{
    public function listAction()
    {
        $this->view->gridModel = new Application_Model_Grid_EmailLog();
    }
}
