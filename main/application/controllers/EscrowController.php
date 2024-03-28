<?php

use Application_Model_Entity_Accounts_User as User;

class EscrowController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $user = User::getCurrentUser();
        if (!$user->isAdmin()) {
            $this->_redirect('/');
        }
        $this->view->gridModel = new Application_Model_Grid_Escrow_EscrowAccount();
    }

    public function editAction()
    {
    }
}
