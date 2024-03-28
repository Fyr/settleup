<?php

use Application_Model_Entity_Accounts_User as User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Application_Model_Rest
{
    /** @var Client */
    protected $client;
    protected $baseUrl;
    protected $credentials;

    public function __construct()
    {
        $this->client = new Client();
        $config = Zend_Registry::get('options');
        $this->baseUrl = $config['auth']['domain'];
    }

    public function login($id, $password, $carrierId = null)
    {
        try {
            $url = $this->baseUrl . '/auth/login/' . $id . '/' . $password;
            $url .= $carrierId ? '/' . $carrierId : '';
            $this->getLogger()->info("going to authenticate user id = $id using the following URL = $url");
            $response = $this->client->get($url);
            if ($response->getStatusCode() == 200) {
                $this->getLogger()->info("user id = $id was successfully authenticated.");
                $data = json_decode($response->getBody(true), true, 512, JSON_THROW_ON_ERROR);
                if (is_array($data) && isset($data['credentials'])) {
                    $this->credentials = $data['credentials'];

                    return $data;
                }
            }
        } catch (RequestException $e) {
            $this->getLogger()->info("unable authenticate user id = $id: ".$e->getMessage());
            $errMsg = null;
            if ($e->hasResponse()) {
                $body = json_decode((string) $e->getResponse()->getBody(), true, 512, JSON_THROW_ON_ERROR);
                $errMsg = $body['error'] ?? null;
            }

            $errMsg ??= 'Unrecognized authorization error';

            throw new AccessDeniedException($errMsg);
        }

        return false;
    }

    public function isCredentialsExists()
    {
        return (bool)$this->credentials;
    }

    public function getCredentials()
    {
        if (!$this->credentials) {
            $this->credentials = User::getCurrentUser()->getCredentials();
        }

        return $this->credentials;
    }

    public function setCredentials($credentials)
    {
        $this->credentials = $credentials;

        return $this;
    }

    public function createUser($data)
    {
        try {
            $credential = $this->getCredentials();
            $options = [
                'form_params' => $data,
                'auth' => [$credential['token'], $credential['secret']],
            ];
            $response = $this->client->post($this->baseUrl . '/users', $options);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody(true), true, 512, JSON_THROW_ON_ERROR);
            }
        } catch (Exception $e) {
            $this->getLogger()->info('Create user error: ' . $e->getMessage());

            return false;
        }

        return false;
    }

    public function createUserSso($data)
    {
        try {
            $options = [
                'form_params' => $data,
            ];
            $response = $this->client->post($this->baseUrl . '/users/sso', $options);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody(true), true, 512, JSON_THROW_ON_ERROR);
            }
        } catch (Exception $e) {
            $this->getLogger()->info('Create user sso error: ' . $e->getMessage());

            return false;
        }

        return false;
    }

    public function updateUser($data)
    {
        try {
            $credential = $this->getCredentials();
            $options = [
                'form_params' => $data,
                'auth' => [$credential['token'], $credential['secret']],
            ];
            $userId = $data['id'];
            $url = $this->baseUrl . '/users/' . $userId;
            $this->getLogger()->debug("going to update user id = $userId using the following URL = $url, and options = ");
            $this->getLogger()->debug(json_encode($options, JSON_THROW_ON_ERROR));
            $response = $this->client->put($url, $options);
            if ($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody(true), true, 512, JSON_THROW_ON_ERROR);

                return $data;
            }
        } catch (Exception) {
            return false;
        }

        return false;
    }

    public function getUser($id)
    {
        $credential = $this->getCredentials();

        try {
            $response = $this->client->get(
                $this->baseUrl . '/users/' . $id,
                ['auth' => [$credential['token'], $credential['secret']]]
            );
        } catch (Exception) {
            return false;
        }
        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody(true), true, 512, JSON_THROW_ON_ERROR);
        }

        return false;
    }

    public function getUserResetPasswordHash($id)
    {
        try {
            $url = $this->baseUrl . '/auth/hash/' . $id;
            $response = $this->client->get($url);
            if ($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody(true), true, 512, JSON_THROW_ON_ERROR);
                if (is_array($data) && isset($data['hash'])) {
                    return $data['hash'];
                }
            }
        } catch (Exception) {
            return false;
        }

        return false;
    }

    public function updatePassword($id, $hash, $newPassword)
    {
        try {
            $url = $this->baseUrl . '/auth/reset/' . $id . '/' . $hash . '/' . $newPassword;
            $response = $this->client->get($url);
            if ($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody(true), true, 512, JSON_THROW_ON_ERROR);
                if (is_array($data) && isset($data['user'])) {
                    return $data['user'];
                }
            }
        } catch (Exception) {
            return false;
        }

        return false;
    }

    public function getCarrierKey($carrierId)
    {
        $credential = $this->getCredentials();

        try {
            $response = $this->client->get(
                $this->baseUrl . '/carrier-keys/' . $carrierId,
                [
                    'auth' => [
                        $credential['token'],
                        $credential['secret'],
                    ],
                ]
            );
        } catch (Exception) {
            return false;
        }
        if ($response->getStatusCode() == 200) {
            return json_decode($response->getBody(true), true, 512, JSON_THROW_ON_ERROR);
        }

        return false;
    }

    /**
     * @param $id
     * @param $password
     * @return bool
     */
    public function checkPassword($id, $password, $entityId = null)
    {
        try {
            $url = $this->baseUrl . '/auth/login/' . (int)$id . '/' . $password . '/' . ($entityId ?: User::getCurrentUser(
            )->getCarrierEntityId());
            $response = $this->client->get($url);
            if ($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody(true), true, 512, JSON_THROW_ON_ERROR);
                if (is_array($data) && isset($data['credentials'])) {
                    return true;
                }
            }
        } catch (Exception) {
            return false;
        }

        return false;
    }

    /**
     * return logger instance
     */
    protected function getLogger()
    {
        return Zend_Registry::get('logger');
    }
}

class AccessDeniedException extends Exception
{
}
