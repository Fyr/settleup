<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_System_UserRoles as Roles;

class Application_Views_Helpers_TopUserMenu extends Zend_View_Helper_Abstract
{
    public function topUserMenu()
    {
        $user = User::getCurrentUser();
        $roleId = $user->getUserRoleID();
        if (!$roleId) {
            return '';
        }
        $roles = [
            Roles::SUPER_ADMIN_ROLE_ID => 'admin.phtml',
            Roles::MODERATOR_ROLE_ID => 'moderator.phtml',
            Roles::CARRIER_ROLE_ID => 'carrier.phtml',
            Roles::CONTRACTOR_ROLE_ID => 'contractor.phtml',
            Roles::VENDOR_ROLE_ID => 'vendor.phtml',
            Roles::GUEST_ROLE_ID => 'guest.phtml',
        ];

        return $this->view->partial(
            'topmenu/top_user_menu.phtml',
            ['user' => $user, 'roleTemplate' => 'topmenu/' . $roles[$roleId]]
        );
    }
}
