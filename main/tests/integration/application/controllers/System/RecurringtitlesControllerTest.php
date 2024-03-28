<?php

class System_RecurringtitlesControllerTest extends BaseTestCase
{
    public function testListAction()
    {
        $this->baseTestAction(
            [
                'params' => [
                    'action' => 'index',
                    'controller' => 'system_recurringtitles',
                ],
                'assert' => [
                    'action' => 'list',
                ],
            ]
        );
    }
}
