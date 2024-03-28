<?php

class System_SettlementcyclestatusControllerTest extends BaseTestCase
{
    public function testListAction()
    {
        $this->baseTestAction(
            [
                'params' => [
                    'action' => 'list',
                    'controller' => 'system_settlementcyclestatus',
                ],
            ]
        );
    }
}
