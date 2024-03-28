<?php

class Application_Model_Grid_Header_Disbursements implements Application_Model_Grid_Header_HeaderInterface
{
    public function getData($grid, $view)
    {
        $buttons = [];

        $add = [
            "caption" => "Create New",
            "button_class" => "btn-success create-disbursement-transaction",
            "icon_class" => "icon-plus",
            "url" => $view->url(
                [
                    'controller' => 'transactions_disbursement',
                    'action' => 'new',
                ],
                null,
                true
            ),
        ];

        if (Application_Model_Entity_Accounts_User::getCurrentUser()->hasPermission(
            Application_Model_Entity_Entity_Permissions::DISBURSEMENT_MANAGE
        )) {
            if ($grid->getCycle()->getId() && $grid->getCycle()->getDisbursementStatus(
            ) == Application_Model_Entity_System_PaymentStatus::NOT_APPROVED_STATUS) {
                $buttons['add'] = $add;
            }
        }

        return $buttons;
    }
}
