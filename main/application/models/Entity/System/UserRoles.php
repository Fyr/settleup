<?php

/**
 * @method $this staticLoad($id, $field = null)
 * @method Application_Model_Resource_System_UserRoles getResource()
 */
class Application_Model_Entity_System_UserRoles extends Application_Model_Base_Entity
{
    final public const DEFAULT_ROLE = 'default';
    final public const SUPER_ADMIN_ROLE_ID = 1;
    final public const ADMIN_ROLE_ID = 2;
    final public const MANAGER_ROLE_ID = 3;
    final public const SPECIALIST_ROLE_ID = 4;
    final public const ONBOARDING_ROLE_ID = 5;
    final public const GUEST_ROLE_ID = 6;
}
