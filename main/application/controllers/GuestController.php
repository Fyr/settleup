<?php

class GuestController extends Zend_Controller_Action
{
    public function indexAction(): void
    {
        $this->view->message = 'Your account has been successfully created. Kindly contact your manager to complete the setup process.';
    }
}
