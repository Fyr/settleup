<?php

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\StaticCall\RemoveParentCallWithoutParentRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\ClassConst\FinalizePublicClassConstantRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Renaming\Rector\Name\RenameClassRector;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/../../application',
        __DIR__ . '/../../public',
        __DIR__ . '/../../src',
    ]);

    $rectorConfig->importNames();
    $rectorConfig->importShortClasses();
    $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);
    $rectorConfig->skip([RemoveParentCallWithoutParentRector::class]);
    $rectorConfig->skip([RenameClassRector::class]);
    $rectorConfig->skip([ReadOnlyPropertyRector::class]);
    $rectorConfig->skip([FinalizePublicClassConstantRector::class]);
    $rectorConfig->skip([ClassPropertyAssignToConstructorPromotionRector::class]);
    $rectorConfig->phpstanConfig(__DIR__ . '/../phpstan/config.neon');
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_81
    ]);
};
