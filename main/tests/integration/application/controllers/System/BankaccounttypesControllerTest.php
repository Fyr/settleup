<?php

class System_BankaccounttypesControllerTest extends BaseTestCase
{
    public function testListAction()
    {
        $this->baseTestAction(
            [
                'params' => [
                    'action' => 'index',
                    'controller' => 'system_bankaccounttypes',
                ],
                'assert' => [
                    'action' => 'error',
                    'controller' => 'error',
                ],
            ]
        );
    }
}
