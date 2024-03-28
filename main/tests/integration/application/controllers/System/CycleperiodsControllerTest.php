<?php

class System_CycleperiodsControllerTest extends BaseTestCase
{
    public function testListAction()
    {
        $this->baseTestAction(
            [
                'params' => [
                    'action' => 'list',
                    'controller' => 'system_cycleperiods',
                ],
            ]
        );
    }
}
