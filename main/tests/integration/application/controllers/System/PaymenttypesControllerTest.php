<?php

class System_PaymenttypesControllerTest extends BaseTestCase
{
    public function testListAction()
    {
        $this->baseTestAction(
            [
                'params' => [
                    'action' => 'list',
                    'controller' => 'system_paymenttypes',
                ],
            ]
        );
    }
}
