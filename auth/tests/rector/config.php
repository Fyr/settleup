<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/../../app',
        __DIR__ . '/../../bootstrap',
        __DIR__ . '/../../config',
        __DIR__ . '/../../database',
        __DIR__ . '/../../public',
        __DIR__ . '/../../routes',
        __DIR__ . '/../../tests',
    ]);

    $rectorConfig->importNames();
    $rectorConfig->importShortClasses();
    $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);
    $rectorConfig->phpstanConfig(__DIR__ . '/../phpstan/config.neon');
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82,
    ]);
};
