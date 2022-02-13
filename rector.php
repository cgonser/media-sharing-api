<?php

declare(strict_types=1);


use Rector\Php80\Rector\Class_\AnnotationToAttributeRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php80\ValueObject\AnnotationToAttribute;
use Rector\Set\ValueObject\SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters
    // $parameters = $containerConfigurator->parameters();

    // Define what rule sets will be applied
//    $containerConfigurator->import(SetList::DEAD_CODE);
    $containerConfigurator->import(SetList::PHP_80);
    $containerConfigurator->import(SetList::PSR_4);
    $containerConfigurator->import(SetList::CODE_QUALITY);
    $containerConfigurator->import(SetList::CODING_STYLE);

    // get services(needed for register a single rule)
    $services = $containerConfigurator->services();
    $services->set(ClassPropertyAssignToConstructorPromotionRector::class);
//    $services->set(AnnotationToAttributeRector::class);

//    $services->set(AnnotationToAttributeRector::class)
//        ->call('configure', [[
//            AnnotationToAttributeRector::ANNOTATION_TO_ATTRIBUTE => ValueObjectInliner::inline([
//                new AnnotationToAttribute(
//                    'Symfony\Component\Routing\Annotation\Route',
//                ),
//            ]),
//        ]]);

};
