<?php

use Application_Model_Entity_Accounts_User as User;
use ApplicationInsights\Telemetry_Client;
use ApplicationInsights\Telemetry_Context;

class Application_Service_Azure_Logger extends Zend_Log_Writer_Abstract
{
    private Telemetry_Client $client;
    private Telemetry_Context $context;

    public function __construct()
    {
        $options = Zend_Registry::getInstance()->options;
        $instrumentationKey = $options['azure']['insightsInstrumentationKey'];
        $this->client = new Telemetry_Client();
        $this->context = $this->client->getContext();
        $this->context->setInstrumentationKey($instrumentationKey);
        $this->context->getSessionContext()->setId(session_id());
        $this->context->getApplicationContext()->setVer('Main');
        $this->context->getUserContext()->setId(User::getCurrentUser()->getId());
    }

    public function shutdown(): void
    {
        $this->client->flush();
    }

    public function __destruct()
    {
        $this->client->flush();
    }

    public static function factory($config): Zend_Log_Writer_Abstract
    {
        $options = Zend_Registry::getInstance()->options;
        if (!$options['azure']['insightsLogger']) {
            return new Zend_Log_Writer_Null();
        }

        return new self();
    }

    protected function _write($event): void
    {
        $message = $event['message'] ?? null;
        if ($message instanceof Throwable) {
            $this->client->trackException($message);

            return;
        }

        $this->client->trackEvent($message, $event);
    }
}
