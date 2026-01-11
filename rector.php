<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Core\Configuration\Option;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Php82\Rector\Class_\ReadOnlyClassRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonyLevelSetList;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
//        __DIR__ . '/tests',
    ]);
//    $rectorConfig->parallel();

    $rectorConfig->importNames();
    $rectorConfig->skip([
        __DIR__ . '/config/bundles.php',
//        __DIR__ . '/src/EconumoBundle/Infrastructure/Doctrine/Migration/*.php',
//        __DIR__ . '/tests/_*',
        ReadOnlyClassRector::class => [
            __DIR__ . '/src/*Bundle/Domain/Entity/*.php',
        ],
        ReadOnlyPropertyRector::class => [
            __DIR__ . '/src/*Bundle/Domain/Entity/*.php',
        ],
    ]);

    // define sets of rules
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82,

        SymfonySetList::SYMFONY_54,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
        SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,

        SetList::CODING_STYLE,
        SetList::CODE_QUALITY,
        SetList::TYPE_DECLARATION,
//        SetList::DEAD_CODE,
//        SetList::PRIVATIZATION,
    ]);
};
