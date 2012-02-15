<?php

namespace Symfony\Cmf\Bundle\BlockBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class BlockCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $manager = $container->getDefinition('sonata.block.manager');

        foreach ($container->findTaggedServiceIds('symfony_cmf.block') as $id => $attributes) {
            $manager->addMethodCall('addBlockService', array($id, $id));
        }
    }
}
