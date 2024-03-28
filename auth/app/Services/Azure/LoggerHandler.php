<?php

namespace App\Services\Azure;

use App\Models\UserToken;
use ApplicationInsights\Telemetry_Client;
use ApplicationInsights\Telemetry_Context;
use Error;
use Exception;
use Illuminate\Support\Facades\Auth;
use Monolog\DateTimeImmutable;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

class LoggerHandler extends AbstractProcessingHandler
{
    private readonly Telemetry_Client $client;
    private readonly Telemetry_Context $context;

    public function __construct()
    {
        $this->client = new Telemetry_Client();
        $this->context = $this->client->getContext();
        $this->context->setInstrumentationKey(env('AZURE_INSIGHT_INSTRUMENTATION_KEY'));
        $this->context->getSessionContext()->setId(session_id());
        $this->context->getApplicationContext()->setVer('Auth');
        $userToken = Auth::getUser();
        $userId = $userToken instanceof UserToken ? $userToken->user_id : null;
        $this->context->getUserContext()->setId($userId);
        parent::__construct();
    }

    public function write(LogRecord $record): void
    {
        $exception = $record->context['exception'] ?? null;
        if ($exception instanceof Error || $exception instanceof Exception) {
            $this->client->trackException($exception);

            return;
        }
        $properties = $record->toArray();
        $datetime = $properties['datetime'];
        if ($datetime instanceof DateTimeImmutable) {
            $properties['datetime'] = $datetime->jsonSerialize();
        }
        $this->client->trackEvent($record->message, $properties);
    }

    public function close(): void
    {
        $this->client->flush();
    }
}
