<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;
use Rector\Symfony\Set\SymfonySetList;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    // register single rule
    ->withRules([
        TypedPropertyFromStrictConstructorRector::class
    ])
    ->withSets([
        SymfonySetList::SYMFONY_70,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
        LevelSetList::UP_TO_PHP_80,
    ])
    ->skip([
        __DIR__.'/**/_generated/*',
    ])
    ->withAttributesSets(symfony: true, doctrine: true)
    ->withPreparedSets(typeDeclarations: true)
//    ->withTypeCoverageLevel(1)
    // here we can define, what prepared sets of rules will be applied
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true
    )
    ->importNames()
    ->importShortClasses(false)
;
