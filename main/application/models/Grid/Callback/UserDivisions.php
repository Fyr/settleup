<?php

use Application_Model_Entity_System_UserRoles as UserRoles;

class Application_Model_Grid_Callback_UserDivisions
{
    use Application_Model_Grid_Callback_BaseTrait;

    public function body(): ?string
    {
        if (in_array($this->row['role_id'], [UserRoles::SUPER_ADMIN_ROLE_ID, UserRoles::ADMIN_ROLE_ID])) {
            return 'All';
        }

        if (!$this->column || $this->row['role_id'] == UserRoles::GUEST_ROLE_ID) {
            return '-';
        }

        return $this->column;
    }
}
