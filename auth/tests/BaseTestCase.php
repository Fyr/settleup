<?php

// namespace Tests;

use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\TestCase;

abstract class BaseTestCase extends TestCase
{
    protected const PASSWORD_HASH = '1a1dc91c907325c69271ddf0c944bc72';

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    protected function getActualCredentials(int $userID)
    {
        $response = $this->call('GET', '/auth/login/'.$userID.'/'.self::PASSWORD_HASH);
        $data = json_decode((string) $response->getContent(), true);

        return [
            'user' => $data['credentials']['token'],
            'password' => $data['credentials']['secret'],
        ];
    }

    protected function createUser()
    {

    }
}
