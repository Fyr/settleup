<?php

// This is global bootstrap for autoloading

\Codeception\Util\Autoload::registerSuffix('Page', __DIR__.DIRECTORY_SEPARATOR.'_pages');
$ds = DIRECTORY_SEPARATOR;
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__  . $ds . 'acceptance' . $ds . 'Input_Data.php';
require_once __DIR__  . $ds . 'acceptance' . $ds . 'BaseSelectors.php';
require_once __DIR__  . $ds . '_pages' . $ds . 'BaseFunctionsPage.php';
require_once __DIR__  . $ds . 'acceptance' . $ds . 'Customers' . $ds . 'Customers_Data.php';
require_once __DIR__  . $ds . 'acceptance' . $ds . 'Vendors' . $ds . 'Vendors_Data.php';
require_once __DIR__  . $ds . 'acceptance' . $ds . 'Contractors' . $ds . 'Contractors_Data.php';
require_once __DIR__  . $ds . '_pages' . $ds . 'LoginPage.php';
require_once __DIR__  . $ds . 'acceptance' . $ds . 'Reserves' . $ds . 'Reserves_Data.php';
require_once __DIR__  . $ds . 'acceptance' . $ds . 'Payments' . $ds . 'Payments_Data.php';
require_once __DIR__  . $ds . 'acceptance' . $ds . 'Deductions' . $ds . 'Deduction_Data.php';
require_once __DIR__  . $ds . 'acceptance' . $ds . 'Settlement' . $ds . 'Settlement_Data.php';
require_once __DIR__  . $ds . 'acceptance' . $ds . 'Disbursements' . $ds . 'Disbursement_Data.php';
require_once __DIR__  . $ds . 'acceptance' . $ds . '11.System' . $ds . 'System_Data.php';
//require_once '' . $ds . 'codeception' . $ds . 'acceptance' . $ds . '_pages' . $ds . 'LoggedUserPage.php';
\Codeception\Util\Autoload::registerSuffix('Group', __DIR__.DIRECTORY_SEPARATOR.'_groups');