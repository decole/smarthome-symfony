<?php

namespace App\DependencyInjection;

use App\Application\Service\PeriodicHandle\Criteria\PeriodicHandleCriteriaInterface;
use App\Application\Service\PeriodicHandle\CriteriaChainService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PeriodicHandleCriteriaCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(CriteriaChainService::class)) {
            return;
        }

        $definition = $container->findDefinition(CriteriaChainService::class);

        // find all service IDs with the periodic.criteria tag
        $taggedServices = $container->findTaggedServiceIds('periodic.criteria');

        foreach ($taggedServices as $id => $tags) {
            /** @var PeriodicHandleCriteriaInterface $class */
            $class = $container->getDefinition($id)->getClass();

            // add criteria to the CriteriaChainService
            $definition->addMethodCall('addCriteria', [new Reference($id), $class::alias()]);
        }
    }
}