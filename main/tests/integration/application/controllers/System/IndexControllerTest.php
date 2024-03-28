<?php

class System_IndexControllerTest extends BaseTestCase
{
    public function testListAction()
    {
        $this->baseTestAction(
            [
                'params' => [
                    'action' => 'list',
                    'controller' => 'system_index',
                ],
            ]
        );
    }

    public function testIndexAction()
    {
        $this->baseTestAction(
            [
                'params' => [
                    'action' => 'index',
                    'controller' => 'system_index',
                ],
                'assert' => [
                    'action' => 'list',
                ],
            ]
        );
    }
}
