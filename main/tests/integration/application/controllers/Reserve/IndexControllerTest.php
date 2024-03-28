<?php

class Reserve_IndexControllerTest extends BaseTestCase
{
    /** @var Reserve_IndexController */
    private $controller;

    public function testIndexAction()
    {
        $this->baseTestAction(
            [
                'params' => [
                    'action' => 'index',
                    'controller' => 'reserve_index',
                ],
            ]
        );
    }
}
