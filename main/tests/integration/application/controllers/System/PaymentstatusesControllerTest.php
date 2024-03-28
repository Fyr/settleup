<?php

class System_PaymentstatusesControllerTest extends BaseTestCase
{
    public function testListAction()
    {
        $this->baseTestAction(
            [
                'params' => [
                    'action' => 'list',
                    'controller' => 'system_paymentstatuses',
                ],
            ]
        );
    }
}
