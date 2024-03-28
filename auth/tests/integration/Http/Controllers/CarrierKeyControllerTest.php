<?php

use App\Http\Middleware\BasicAuthenticate;

class CarrierKeyControllerTest extends BaseTestCase
{
    /**
        show() tests
        TODO:
        should create credentials if carrier found
        should create credentials if carrier is not found
    **/
    public function testShouldFailForIncorrectAuthHeaders()
    {
        $userID = 3;
        $credentials = $this->getActualCredentials($userID);

        $response = $this->call('GET', '/carrier-keys/1', [], [], [], [
            'PHP_AUTH_USER' => $credentials['user'].'!',
            'PHP_AUTH_PW' => $credentials['password'],
        ]);
        $this->assertEquals($response->getStatusCode(), 401);
        $this->assertEquals($response->getContent(), BasicAuthenticate::INVALID_CREDENTIALS);
    }
}
