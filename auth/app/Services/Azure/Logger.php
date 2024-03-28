<?php

namespace App\Services\Azure;

use Monolog\Logger as MongoLogger;

class Logger
{
    public function __invoke(array $config): MongoLogger
    {
        return new MongoLogger(
            $config['appName'],
            [
                new LoggerHandler(),
            ]
        );
    }
}
