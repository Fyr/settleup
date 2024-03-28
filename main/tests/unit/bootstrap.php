<?php
echo "Bootstrapping for Unit tests...\n";
error_reporting(E_ERROR);
// Define path to application directory
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(__DIR__ . '/../../application'));

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ?: 'testing'));

// Ensure library/ is on include_path
set_include_path(
    implode(
        PATH_SEPARATOR,
        [
            APPLICATION_PATH,
            dirname(APPLICATION_PATH) . '/library',
            get_include_path(),
        ]
    )
);

// Init autoloaders
require_once __DIR__ . '/../../vendor/autoload.php';
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();

// Init app and autoload app classes
$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
$bootstrap = new Zend_Application(APPLICATION_ENV, $config);
Zend_Registry::set('bootstrap', $bootstrap);

echo "OK\n";
