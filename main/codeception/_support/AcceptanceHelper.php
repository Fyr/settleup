<?php
namespace Codeception\Module;

use AcceptanceTester;


class AcceptanceHelper extends \Codeception\Module
{
 function getBaseUrl()
 {
     return $this->getModule('WebDriver')->_getUrl();
 }

}