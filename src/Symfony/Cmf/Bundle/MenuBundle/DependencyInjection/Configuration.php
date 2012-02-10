<?php

namespace Symfony\Cmf\Bundle\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('symfony_cmf_menu');

        $rootNode
            ->children()
                ->scalarNode('menu_basepath')->defaultValue('/cms/menu')->end()
                ->scalarNode('document_manager')->defaultValue('doctrine_phpcr.odm.default_document_manager')->end()
                ->scalarNode('menu_document_class')->defaultNull()->end()

                ->scalarNode('content_url_generator')->defaultValue('symfony_cmf_chain_routing.doctrine_router')->end()
                ->scalarNode('content_key')->defaultNull()->end()
                ->scalarNode('route_name')->defaultNull()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
