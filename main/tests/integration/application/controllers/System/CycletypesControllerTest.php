<?php

class System_CycletypesControllerTest extends BaseTestCase
{
    public function testListAction()
    {
        $this->baseTestAction(
            [
                'params' => [
                    'action' => 'list',
                    'controller' => 'system_cycletypes',
                ],
            ]
        );
    }
}
