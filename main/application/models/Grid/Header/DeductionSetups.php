<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_Entity_Permissions as Permissions;

class Application_Model_Grid_Header_DeductionSetups implements Application_Model_Grid_Header_HeaderInterface
{
    public function getData($grid, $view)
    {
        $buttons = [];
        if (!User::getCurrentUser()->hasPermission(Permissions::TEMPLATE_MANAGE)) {
            return $buttons;
        }

        $delete = [
            "caption" => "Delete Selected",
            "button_class" => "btn-danger confirm confirm-delete btn-multiaction",
            "confirm-type" => "Deletion",
            "delete-second-message" => "1",
            "confirm-message" => 'Deleting the master template will result in the deletion of all associated individual templates.<br/><br/> Do you still want to delete?',
            "icon_class" => "icon-trash",
            "action-type" => "delete",
            "url" => $view->url(['controller' => 'deductions_setup', 'action' => 'multiaction'], null, true),
        ];

        $add = [
            "caption" => "Create New",
            "button_class" => "btn-success",
            "icon_class" => "icon-plus",
            "url" => $view->url(['controller' => 'deductions_setup', 'action' => 'new',], null, true),
        ];

        $buttons['delete'] = $delete;
        $buttons['add'] = $add;

        return $buttons;
    }
}
