<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration for the PHPCR extension
 *
 * @author Lukas Kahwe Smith <smith@pooteeweet.org>
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class Configuration implements ConfigurationInterface
{
    private $debug;

    /**
     * Constructor
     *
     * @param Boolean $debug Whether to use the debug mode
     */
    public function  __construct($debug)
    {
        $this->debug = (Boolean) $debug;
    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('doctrine_phpcr')
            ->children()
                ->scalarNode('jackrabbit_jar')->end()
                ->scalarNode('dump_max_line_length')->defaultValue(120)->end()
            ->end()
        ;

        $this->addPHPCRSection($rootNode);
        $this->addOdmSection($rootNode);

        return $treeBuilder;
    }

    private function addPHPCRSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
            ->arrayNode('session')
                ->isRequired()
                ->beforeNormalization()
                    ->ifTrue(function ($v) { return !is_array($v) || (is_array($v) && !array_key_exists('sessions', $v) && !array_key_exists('session', $v)); })
                    ->then(function ($v) {
                        if (!is_array($v)) {
                            $v = array();
                        }

                        $session = array();
                        foreach (array(
                            'workspace',
                            'username',
                            'password',
                            'backend',
                        ) as $key) {
                            if (array_key_exists($key, $v)) {
                                $session[$key] = $v[$key];
                                unset($v[$key]);
                            }
                        }
                        $v['default_session'] = isset($v['default_session']) ? (string) $v['default_session'] : 'default';
                        $v['sessions'] = array($v['default_session'] => $session);

                        return $v;
                    })
                ->end()
                ->children()
                    ->scalarNode('default_session')->end()
                ->end()
                ->fixXmlConfig('session')
                ->append($this->getPHPCRSessionsNode())
            ->end()
        ;
    }

    private function getPHPCRSessionsNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('sessions');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->scalarNode('workspace')->isRequired()->cannotBeEmpty()->end()
                    ->scalarNode('username')->defaultNull()->end()
                    ->scalarNode('password')->defaultNull()->end()
                    ->arrayNode('backend')
                        ->useAttributeAsKey('name')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    private function addOdmSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('odm')
                    ->beforeNormalization()
                        ->ifTrue(function ($v) { return null === $v || (is_array($v) && !array_key_exists('document_managers', $v) && !array_key_exists('document_manager', $v)); })
                        ->then(function ($v) {
                            $v = (array) $v;
                            $documentManagers = array();
                            foreach (array(
                                'auto_mapping', 'auto-mapping',
                                'mappings', 'mapping',
                                'session',
                            ) as $key) {
                                if (array_key_exists($key, $v)) {
                                    $documentManagers[$key] = $v[$key];
                                    unset($v[$key]);
                                }
                            }
                            $v['default_document_manager'] = isset($v['default_document_manager']) ? (string) $v['default_document_manager'] : 'default';
                            $v['document_managers'] = array($v['default_document_manager'] => $documentManagers);

                            return $v;
                        })
                    ->end()
                    ->children()
                        ->scalarNode('default_document_manager')->end()
                    ->end()
                    ->fixXmlConfig('document_manager')
                    ->append($this->getOdmDocumentManagersNode())
                ->end()
            ->end()
        ;
    }

    private function getOdmDocumentManagersNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('document_managers');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('session')->end()
                    ->scalarNode('auto_mapping')->defaultFalse()->end()
                ->end()
                ->fixXmlConfig('mapping')
                ->fixXmlConfig('design_document')
                ->children()
                    ->arrayNode('mappings')
                        ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->beforeNormalization()
                                ->ifString()
                                ->then(function($v) { return array('type' => $v); })
                            ->end()
                            ->treatNullLike(array())
                            ->treatFalseLike(array('mapping' => false))
                            ->performNoDeepMerging()
                            ->children()
                                ->scalarNode('mapping')->defaultValue(true)->end()
                                ->scalarNode('type')->end()
                                ->scalarNode('dir')->end()
                                ->scalarNode('alias')->end()
                                ->scalarNode('prefix')->end()
                                ->booleanNode('is_bundle')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
