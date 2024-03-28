<?php

class System_UsercontacttypesControllerTest extends BaseTestCase
{
    public function testListAction()
    {
        $this->baseTestAction(
            [
                'params' => [
                    'action' => 'list',
                    'controller' => 'system_usercontacttypes',
                ],
            ]
        );
    }
}
