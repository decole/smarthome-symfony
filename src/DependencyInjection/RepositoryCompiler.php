<?php

namespace App\DependencyInjection;

use ReflectionClass;
use ReflectionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class RepositoryCompiler implements CompilerPassInterface
{
    private const REPOSITORY_POSTFIX = 'RepositoryInterface';

    /**
     * @throws ReflectionException
     */
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('doctrine.repository') as $id => $_) {
            $class = $container->getDefinition($id)->getClass();

            if (null === $class) {
                continue;
            }

            $implementation = new ReflectionClass($class);

            foreach ($implementation->getInterfaces() as $reflectionClass) {
                if (mb_substr(self::REPOSITORY_POSTFIX, -mb_strlen($reflectionClass->name))) {
                    $container->setAlias($reflectionClass->name, $implementation->name);
                }
            }
        }
    }
}