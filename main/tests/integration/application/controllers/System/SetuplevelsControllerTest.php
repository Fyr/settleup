<?php

class System_SetuplevelsControllerTest extends BaseTestCase
{
    public function testListAction()
    {
        $this->baseTestAction(
            [
                'params' => [
                    'action' => 'list',
                    'controller' => 'system_setuplevels',
                ],
            ]
        );
    }
}
