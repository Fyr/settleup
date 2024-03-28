<?php

class System_SystemvaluesControllerTest extends BaseTestCase
{
    public function testListAction()
    {
        $this->baseTestAction(
            [
                'params' => [
                    'action' => 'list',
                    'controller' => 'system_systemvalues',
                ],
            ]
        );
    }
}
