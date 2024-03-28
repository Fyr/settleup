<?php

use App\Exceptions\InvalidPasswordException;
use App\Exceptions\UserNotFoundException;
use App\Http\Middleware\BasicAuthenticate;

class AuthControllerTest extends BaseTestCase
{
    /**
        Pre-conditions test for DB
    **/
    public function testCheckDatabase()
    {
        $role_id = 1;
        $password = '$2y$10$MqTKvc51cdpxIQznIhtuwOaBOW2V2cjFuo5nBAFeY.ZDIFqId4iye';
        $created_at = '2015-03-15 12:00:00';
        //$updated_at = $created_at;
        $deleted_at = null;
        for ($id = 1; $id < 4; $id++) {
            $this->seeInDatabase('users', compact('id', 'role_id', 'password', 'created_at', 'deleted_at'));
        }
    }

    /**
        Login() tests
    **/
    public function testShouldFailWithNonexistedUser()
    {
        $response = $this->call('GET', '/auth/login/123/'.self::PASSWORD_HASH);
        $e = new UserNotFoundException();

        $this->assertEquals($response->getStatusCode(), $e->getStatusCode());
        $this->assertEquals(json_decode((string) $response->getContent(), true), ['error' => $e->getMessage()]);
    }

    public function testShouldFailWithInvalidUser()
    {
        $response = $this->call('GET', '/auth/login/asd123/'.self::PASSWORD_HASH);

        // according to routing as URL does not match any route (because user ID is alfa numeric but not int)
        // app must throw HTTP 404 Page Not Found
        $this->assertEquals($response->getStatusCode(), 404);
    }

    public function testShouldLoginWithCorrectPassword()
    {
        $userID = 3;
        $response = $this->call('GET', '/auth/login/'.$userID.'/'.self::PASSWORD_HASH);
        $this->assertEquals($response->getStatusCode(), 200);

        $data = json_decode((string) $response->getContent(), true);

        $this->assertArrayHasKey('credentials', $data);
        $this->assertArrayHasKey('user_id', $data['credentials']);
        $this->assertArrayHasKey('token', $data['credentials']);
        $this->assertArrayHasKey('secret', $data['credentials']);
        $this->assertEquals($data['credentials']['user_id'], $userID);
    }

    public function testShouldFailWithNoPassword()
    {
        $response = $this->call('GET', '/auth/login/3/');

        // according to routing as URL does not match any route (because there is no password)
        // app must throw HTTP 404 Page Not Found
        $this->assertEquals($response->getStatusCode(), 404);
    }

    public function testShouldFailWithIncorrectPassword()
    {
        $response = $this->call('GET', '/auth/login/3/'.str_replace('1', '2', self::PASSWORD_HASH));
        $e = new InvalidPasswordException();

        $this->assertEquals($response->getStatusCode(), $e->getStatusCode());
        $this->assertEquals(json_decode((string) $response->getContent(), true), ['error' => $e->getMessage()]);
    }

    public function testShouldFailWithIncorrectExcessiveHashForPassword()
    {
        $response = $this->call('GET', '/auth/login/3/'.self::PASSWORD_HASH.'123');
        $e = new InvalidPasswordException();

        $this->assertEquals($response->getStatusCode(), $e->getStatusCode());
        $this->assertEquals(json_decode((string) $response->getContent(), true), ['error' => $e->getMessage()]);
    }

    /**
        Headers validation tests
    **/
    public function testShouldFailIfNoAuthHeaders()
    {
        $response = $this->call('GET', '/users/3');
        $this->assertEquals($response->getStatusCode(), 401);
        $this->assertEquals($response->getContent(), BasicAuthenticate::NO_AUTH_HEADER);
    }

    public function testShouldIgnoreAuthHeadersForLoginRequest()
    {
        $response = $this->call('GET', '/auth/login/3/'.self::PASSWORD_HASH);
        $this->assertEquals($response->getStatusCode(), 200);
    }

    public function testShouldAcceptCorrectAuthHeaders()
    {
        $userID = 3;
        $credentials = $this->getActualCredentials($userID);

        $response = $this->call('GET', '/users/'.$userID, [], [], [], [
            'PHP_AUTH_USER' => $credentials['user'],
            'PHP_AUTH_PW' => $credentials['password'],
        ]);
        $this->assertEquals($response->getStatusCode(), 200);
    }

    public function testShouldFailForIncorrectAuthHeadersWithUsername()
    {
        $userID = 3;
        $credentials = $this->getActualCredentials($userID);

        $response = $this->call('GET', '/users/'.$userID, [], [], [], [
            'PHP_AUTH_USER' => $credentials['user'].'!',
            'PHP_AUTH_PW' => $credentials['password'],
        ]);
        $this->assertEquals($response->getStatusCode(), 401);
        $this->assertEquals($response->getContent(), BasicAuthenticate::INVALID_CREDENTIALS);
    }
}
