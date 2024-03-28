<?php

class Application_Views_Helpers_AddApprove extends Zend_View_Helper_Abstract
{
    /**
     * If in current form status does not approve, add button approve
     * return <div> peace of html </div>
     *
     * @return string
     */
    public function addApprove()
    {
        $approve = '';
        $status = $this->view->form->status->getValue();
        $id = $this->view->form->id->getValue();
        if ($status !== (string)Application_Model_Entity_System_PaymentStatus::APPROVED_STATUS && $id) {
            $approve = '<div class="right">' . $this->view->setButtons(['approve'], $id) . '</div>';
        }

        return $approve;
    }
}
