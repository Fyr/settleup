<?php

class System_ContractorstatusesControllerTest extends BaseTestCase
{
    public function testListAction()
    {
        $this->baseTestAction(
            [
                'params' => [
                    'action' => 'list',
                    'controller' => 'system_contractorstatuses',
                ],
            ]
        );
    }
}
