<?php

class Transactions_IndexControllerTest extends BaseTestCase
{
    /** @var Transactions_IndexController */
    private $controller;

    protected function setUp(): void
    {
        $this->setDefaultController('transactions_index');
        parent::setUp();
    }

    public function testIndexAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'index'],
            ]
        );
    }
}
