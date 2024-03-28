<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class Application_Service_Hub
{
    protected Client $_client;
    protected string $_baseUrl;
    protected string $_authToken;

    public function __construct()
    {
        $this->_client = new Client();
        $config = Zend_Registry::get('options');
        $this->_baseUrl = $config['settle_up_api']['url'];
        $this->_authToken = $config['settle_up_api']['auth_token'];
    }

    /**
     * Export settlement cycle to Forward Air Hub
     *
     * @return bool
     */
    public function export_cycle(int $cycleId): bool
    {
        try {
            $options = [];
            if ($token = $this->getAuthToken()) {
                $options = [
                    'headers' => [
                        'Authorization' => 'Basic ' . $token,
                    ],
                ];
            }

            $url = $this->getBaseUrl() . '/cycle/export/' . $cycleId;
            $response = $this->getClient()->post($url, $options);
            if ($response->getStatusCode() == 200) {
                $this->getLogger()->info('Settlement cycle ID = '. $cycleId . ' was successfully exported: ' . $response->getBody());

                return true;
            }
        } catch (ClientException $e) {
            $this->getLogger()->err('Unable to export settlement cycle ID = '. $cycleId . '  due to: ' . $e->getMessage());

            return false;
        }

        return false;
    }

    public function getClient(): Client
    {
        return $this->_client;
    }

    public function getBaseUrl(): string
    {
        return $this->_baseUrl;
    }

    public function getAuthToken(): string
    {
        return $this->_authToken;
    }

    /**
     * return logger instance
     */
    protected function getLogger()
    {
        return Zend_Registry::get('logger');
    }
}
