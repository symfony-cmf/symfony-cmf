<?php
namespace Symfony\Cmf\Bundle\NavigationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('symfony_cmf_navigation');

        $rootNode
            ->children()
                ->arrayNode('controllers_by_alias')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('alias')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('controllers_by_content')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('alias')
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('mainmenu_routename')->defaultValue('navigation')->end()
                ->scalarNode('document')->defaultValue('Symfony\Cmf\Bundle\NavigationBundle\Document\Navigation')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}