<?php

class FreezeControllerTest extends BaseTestCase
{
    /** @var FreezeController */
    private $controller;

    protected function setUp(): void
    {
        $this->setDefaultController('freeze');
        parent::setUp();
    }

    public function testSaveAction()
    {
        $file_name = 'phpunit_freeze_test_23';
        $this->baseTestAction(
            [
                'params' => ['action' => 'save'],
                'post' => [
                    'desc' => $file_name,
                ],
            ]
        );
        $stateSaved = null;
        foreach (glob(APPLICATION_PATH . '/../scripts/db/freezeState/' . '*.gz') as $state) {
            $states[] = basename($state);
            if (strripos(basename($state), $file_name)) {
                $stateSaved = $state;
            }
        }
        $this->assertNotNull($stateSaved);
        return $stateSaved;
    }

    public function testIndexAction()
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'index'],
            ]
        );
    }

    /**
     * @depends testSaveAction
     * @param $state string
     */
    public function testRestoreAction($state)
    {
        $this->baseTestAction(
            [
                'params' => ['action' => 'restore'],
                'post' => [
                    'state' => basename((string) $state),
                ],
            ]
        );
        if ($state) {
            unlink($state);
        }
    }
}
