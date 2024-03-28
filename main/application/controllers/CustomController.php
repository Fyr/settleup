<?php

use Application_Form_CustomFieldNames as Form;
use Application_Model_Entity_Accounts_User as User;

class CustomController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $form = new Form();
        $carrier = User::getCurrentUser()->getSelectedCarrier();
        $entity = $carrier->getCustomFieldNames();
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();
            if ($form->isValid($post)) {
                $entity->setData($form->getValues());
                $entity->save();
                $this->_helper->redirector('index', 'settlement_index');
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } else {
            $form->populate($entity->getData());
        }
    }
}
