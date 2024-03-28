<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;

class Application_Model_Grid_Header_SettlementGroup implements Application_Model_Grid_Header_HeaderInterface
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
                    'controller' => 'settlement_group',
                    'action' => 'new',
                ],
                null,
                true
            ),
        ];

        if (User::getCurrentUser()->hasPermission(Permissions::SETTLEMENT_GROUP_MANAGE)) {
            $buttons['add'] = $add;
        }

        return $buttons;
    }
}
