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
            Roles::SUPER_ADMIN_ROLE_ID => 'super-admin.phtml',
            Roles::ADMIN_ROLE_ID => 'admin.phtml',
            Roles::MANAGER_ROLE_ID => 'manager.phtml',
            Roles::SPECIALIST_ROLE_ID => 'specialist.phtml',
            Roles::ONBOARDING_ROLE_ID => 'onboarding.phtml',
            Roles::GUEST_ROLE_ID => 'guest.phtml',
        ];

        return $this->view->partial(
            'topmenu/top_user_menu.phtml',
            ['user' => $user, 'roleTemplate' => 'topmenu/' . $roles[$roleId]]
        );
    }
}
