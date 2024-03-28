<?php

declare(strict_types=1);

// return version
if (isset($_SERVER['SCRIPT_URI']) && $_SERVER['SCRIPT_URI'] == '/version') {
    echo nl2br(file_get_contents('version.txt'));

    return;
}

error_reporting(E_ALL ^ (E_DEPRECATED | E_NOTICE));

// Define path to application directory
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', dirname(__DIR__) . '/application');

// Define application environment
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', getenv('APPLICATION_ENV') ?: 'development');

// Ensure library/ is on include_path
//set_include_path(implode(PATH_SEPARATOR, array(
//    realpath(APPLICATION_PATH . '/../library'),
//    get_include_path(),
//)));

/** Zend_Application */
require_once __DIR__ . '/../vendor/autoload.php';
//require_once 'Zend/Application.php';

if (file_exists(APPLICATION_PATH . '/configs/' . APPLICATION_ENV . '/application.ini')) {
    $options = APPLICATION_PATH . '/configs/' . APPLICATION_ENV . '/application.ini';
} else {
    $options = APPLICATION_PATH . '/configs/application.ini';
}

// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, $options);

$application->bootstrap()
    ->run();

function fdebug($data, $logFile = 'tmp.log', $lAppend = true)
{
    file_put_contents($logFile, mb_convert_encoding(print_r($data, true), 'cp1251', 'utf8'), ($lAppend) ? FILE_APPEND : null);

    return $data;
}
