<?php

use App\Models\User;
use Illuminate\Support\Arr;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UserControllerTest extends BaseTestCase
{
    use DatabaseTransactions;

    /**
        show() tests
    **/
    public function testShouldReturnUserByItsId()
    {
        $userID = 3;
        $credentials = $this->getActualCredentials($userID);

        $expectedUser = User::query()->find($userID)->toArray();

        $response = $this->call('GET', '/users/'.$userID, [], [], [], [
            'PHP_AUTH_USER' => $credentials['user'],
            'PHP_AUTH_PW' => $credentials['password'],
        ]);
        $actualData = json_decode((string) $response->getContent(), true);

        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertEquals($actualData, ['user' => $expectedUser]);
        $this->assertEquals($actualData['user']['id'], $userID);
    }

    public function testShouldCreateUser()
    {
        $userID = 3;
        $credentials = $this->getActualCredentials($userID);
        $data = ['password' => 'pass'];
        $response = $this->call('POST', '/users', $data, [], [], [
            'PHP_AUTH_USER' => $credentials['user'],
            'PHP_AUTH_PW' => $credentials['password'],
        ]);
        $actualData = json_decode((string) $response->getContent(), true);

        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertArrayHasKey('user', $actualData);
        $this->assertArrayHasKey('id', $actualData['user']);
        $this->assertArrayHasKey('created_at', $actualData['user']);
        $this->assertArrayHasKey('updated_at', $actualData['user']);
        $this->assertArrayHasKey('password', $actualData['user']);
    }

    public function testShouldUpdateUser()
    {
        $userID = 3;
        $credentials = $this->getActualCredentials($userID);
        $data = ['role_id' => 2];
        $response = $this->call('PUT', '/users/'.$userID, $data, [], [], [
            'PHP_AUTH_USER' => $credentials['user'],
            'PHP_AUTH_PW' => $credentials['password'],
        ]);
        $actualData = json_decode((string) $response->getContent(), true);

        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertArrayHasKey('user', $actualData);
        $this->assertArrayHasKey('id', $actualData['user']);
        $this->assertArrayHasKey('updated_at', $actualData['user']);
        $this->assertEquals($actualData['user']['id'], $userID);
        $this->assertEquals($actualData['user']['role_id'], $data['role_id']);
    }

    public function testShouldFailToUpdateUserIfNoIdProvided()
    {
        $userID = 3;
        $credentials = $this->getActualCredentials($userID);
        $data = ['role_id' => 2];
        $response = $this->call('PUT', '/users/', $data, [], [], [
            'PHP_AUTH_USER' => $credentials['user'],
            'PHP_AUTH_PW' => $credentials['password'],
        ]);
        $actualData = json_decode((string) $response->getContent(), true);

        $this->assertEquals($response->getStatusCode(), 405);
    }

    public function testShouldRemoveUser()
    {
        $userID = 3;
        $credentials = $this->getActualCredentials($userID);

        $response = $this->call('DELETE', '/users/'.$userID, [], [], [], [
            'PHP_AUTH_USER' => $credentials['user'],
            'PHP_AUTH_PW' => $credentials['password'],
        ]);
        $actualData = json_decode((string) $response->getContent(), true);

        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertEquals($actualData, ['deleted' => true]);

        // check existing users - there must be no removed user in DB
        $existingUserIds = Arr::pluck(User::all()->toArray(), 'id');
        $this->assertFalse(in_array($userID, $existingUserIds));
    }

    public function testShouldFailToRemoveUserIfNoIdProvided()
    {
        $userID = 3;
        $credentials = $this->getActualCredentials($userID);

        $response = $this->call('DELETE', '/users/', [], [], [], [
            'PHP_AUTH_USER' => $credentials['user'],
            'PHP_AUTH_PW' => $credentials['password'],
        ]);

        $this->assertEquals($response->getStatusCode(), 405);
    }

    /**
        TODO:
        cover with integration tests methods: storeSso(), destroy()
    **/
}
