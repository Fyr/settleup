<?php

use Application_Form_InterestRate_InterestRate as InterestRateForm;
use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_InterestRate_InterestRate as InterestRate;

class InterestRate_IndexController extends Zend_Controller_Action
{
    /** @var InterestRate */
    protected $_entity;
    /** @var InterestRateForm */
    protected $_form;
    protected $_title = 'Interest Rate';

    public function init()
    {
        $this->_entity = new InterestRate();
        $this->_form = new InterestRateForm();
    }

    public function indexAction()
    {
        $this->forward('edit');
    }

    public function newAction()
    {
        $this->forward('edit');
    }

    public function editAction()
    {
        $user = User::getCurrentUser();
        $this->view->title = $this->_title;
        $this->view->form = $this->_form;
        $this->view->headTitle()->append($this->_title);
        $latestRateData = $this->_entity->getLatestRateData();
        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if ($this->_form->isValid($post)) {
                $this->_entity->setRate($this->_form->getValue('rate'));
                $this->_entity->setCreatedBy($user->getId());
                $this->_entity->save();

                return $this->_helper->redirector(
                    'index',
                    $this->_getParam('controller')
                );
            } else {
                $this->_form->setEncryptedFields();
                $this->_form->populate($post);
            }
        } elseif ($latestRateData) {
            $this->_form->populate($latestRateData);
        }
    }
}
