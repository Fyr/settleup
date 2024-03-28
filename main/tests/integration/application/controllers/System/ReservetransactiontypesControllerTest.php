<?php

class System_ReservetransactiontypesControllerTest extends BaseTestCase
{
    public function testListAction()
    {
        $this->baseTestAction(
            [
                'params' => [
                    'action' => 'list',
                    'controller' => 'system_reservetransactiontypes',
                ],
            ]
        );
    }
}
