<?php

use Application_Model_Entity_Accounts_User as User;
use Application_Model_Entity_System_UserRoles as UserRoles;

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initOptions()
    {
        Zend_Registry::getInstance()->options = $this->getOptions();
        $frontendOptions = ['automatic_serialization' => true];
        if (php_sapi_name() != 'cli') {
            $backendOptions = ['cache_dir' => APPLICATION_PATH . '/../data/cache'];
            $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
            Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        }
    }

    protected function _initAcl()
    {
        $fc = Zend_Controller_Front::getInstance();
        $fc->registerPlugin(new Application_Plugin_Access());
        $fc->registerPlugin(new Application_Plugin_CurrentController());
        $fc->registerPlugin(new Application_Plugin_Mail());
    }

    public function _initNavigation()
    {
        $this->bootstrapView();
        $view = $this->getResource('view');
        $navigation = new My_Navigation(new Zend_Config(require $this->getConfigName()));
        Zend_Registry::set('Zend_Navigation', $navigation);
        $view->navigation($navigation);
    }

    /**
     * @return string
     */
    public function getConfigName()
    {
        $user = User::getCurrentUser();
        $ds = DIRECTORY_SEPARATOR;
        $prefix = APPLICATION_PATH . $ds . 'configs' . $ds . 'navigation' . $ds;

        $roles = [
            UserRoles::SUPER_ADMIN_ROLE_ID => 'super-admin',
            UserRoles::ADMIN_ROLE_ID => 'admin',
            UserRoles::MANAGER_ROLE_ID => 'manager',
            UserRoles::SPECIALIST_ROLE_ID => 'specialist',
            UserRoles::ONBOARDING_ROLE_ID => 'onboarding',
            UserRoles::GUEST_ROLE_ID => 'guest',
        ];

        if (isset($roles[$user->getRoleId()])) {
            return $prefix . $roles[$user->getRoleId()] . '.php';
        }

        return $prefix . 'default.php';
    }

    protected function _initLogger()
    {
        $this->bootstrap('log');
        $logger = $this->getResource('log');
        Zend_Registry::set('logger', $logger);
    }
}
