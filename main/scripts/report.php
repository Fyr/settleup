<?php

// initialize the application path, library and autoloading
defined('APPLICATION_PATH') ||
define('APPLICATION_PATH', realpath(__DIR__ . '/../application'));

// NOTE: if you already have "library" directory available in your include path
// you don't need to modify the include_path right here
// so in that case you can leave last 4 lines commented
// to avoid receiving error message:
// Fatal error: Cannot redeclare class Zend_Loader in ....
// NOTE: anyway you can uncomment last 4 lines of this comments block
// to manually set the include path directory
 $paths = explode(PATH_SEPARATOR, get_include_path());
 $paths[] = realpath(__DIR__.'/../library');
 set_include_path(implode(PATH_SEPARATOR, $paths));
 unset($paths);

//require_once 'Zend/Loader/Autoloader.php';
//$loader = Zend_Loader_Autoloader::getInstance();

/** Zend_Application */
require_once __DIR__ . '/../vendor/autoload.php';

// we need this custom namespace to load our custom class
//$loader->registerNamespace('My_');

// define application options and read params from CLI
$getopt = new Zend_Console_Getopt(array(
    'action|a=s' => 'action to perform in format of "module/controller/action/param1/param2/param3/.."',
    'env|e-s'    => 'defines application environment (defaults to "production")',
    'help|h'     => 'displays usage information',
    'password|p=s' => 'use password',
    'token|t=s' => 'use token',
    'secret|s=s' => 'use secret',
    'user|u=s' => 'use user',
    'account|r=s' => 'use reserve account'
));

try {
    $getopt->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    // Bad options passed: report usage
    echo $e->getUsageMessage();
    return false;
}

// show help message in case it was requested or params were incorrect (module, controller and action)
if ($getopt->getOption('h') || !$getopt->getOption('a')) {
    echo $getopt->getUsageMessage();
    return true;
}

// initialize values based on presence or absence of CLI options
$env      = $getopt->getOption('e');
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (null === $env) ? 'production' : $env);

// initialize Zend_Application
$application = new Zend_Application (
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

// bootstrap and retrive the frontController resource
$front = $application->getBootstrap()
    ->bootstrap('frontController')
    ->getResource('frontController');

// magic starts from this line!
//
// we will use Zend_Controller_Request_Simple and some kind of custom code
// to emulate missed in Zend Framework ecosystem
// "Zend_Controller_Request_Cli" that can be found as proposal here:
// http://framework.zend.com/wiki/display/ZFPROP/Zend_Controller_Request_Cli
//
// I like the idea to define request params separated by slash "/"
// for ex. "module/controller/action/param1/param2/param3/.."
//
// NOTE: according to the current implementation param1,param2,param3,... are omited
//    only module/controller/action are used
//
// TODO: allow to omit "module", "action" params
//      and set them to "default" and "index" accordantly
//
// so lets split the params we've received from the CLI
// and pass them to the reqquest object
// NOTE: I think this functionality should be moved to the routing
$params = array_reverse(explode('/', $getopt->getOption('a')));
$action = array_pop($params);
$id = $params ? array('id' => array_pop($params)) : array();
$passwordParams = $getopt->getOption('p');
$passwordParams = $passwordParams ? array('password' => $passwordParams) : array();

$secret = $getopt->getOption('s');
$secret = $secret ? array('secret' => $secret) : array();
$token = $getopt->getOption('t');
$token = $token ? array('token' => $token) : array();
$user = $getopt->getOption('u');
$user = $user ? array('user' => $user) : array();
$account = $getopt->getOption('r');
$account = $account ? array('account' => $account) : array();


$request = new Zend_Controller_Request_Simple ($action, 'cron', null, array_merge($id, $passwordParams, $secret, $token, $user, $account));

// set front controller options to make everything operational from CLI
$front->setRequest($request)
    ->setResponse(new Zend_Controller_Response_Cli())
    ->setRouter(new My_Controller_Router_Cli())
    ->throwExceptions(true);

// lets bootstrap our application and enjoy!
$application->bootstrap()
    ->run();
