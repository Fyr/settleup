<?php


use AcceptanceTester\LoggedUserSteps;
use \Codeception\Event\TestEvent;
/**
 * Group class is Codeception Extension which is allowed to handle to all internal events.
 * This class itself can be used to listen events for test execution of one particular group.
 * It may be especially useful to create fixtures data, prepare server, etc.
 *
 * INSTALLATION:
 *
 * To use this group extension, include it to "extensions" option of global Codeception config.
 */

class OneTestGroup extends \Codeception\Platform\Group
{
    public static $group = 'oneTest';

    public function _before(TestEvent $e)
    {
        $this->user = $e;

        $I=new LoggedUserSteps($e);
    }

    public function _after(TestEvent $e)
    {
    }
}