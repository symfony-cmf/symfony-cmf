<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Bundle\DoctrineAbstractBundle\DependencyInjection\AbstractDoctrineExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;

/**
 * PHPCR Extension
 *
 * @author Lukas Kahwe Smith <smith@pooteeweet.org>
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */
class DoctrinePHPCRExtension extends AbstractDoctrineExtension
{
    private $documentManagers;
    
    private $bundleDirs = array();

    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration($container->getParameter('kernel.debug'));
        $config = $processor->processConfiguration($configuration, $configs);

        if (!empty($config['session'])) {
            $this->sessionLoad($config['session'], $container);
        }

        if (!empty($config['odm'])) {
            $this->odmLoad($config['odm'], $container);
        }
    }

    private function sessionLoad($config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('phpcr.xml');

        if (empty($config['default_session'])) {
            $keys = array_keys($config['sessions']);
            $config['default_session'] = reset($keys);
        }
        $this->defaultSession = $config['default_session'];

        $container->setAlias('phpcr.session', sprintf('doctrine_phpcr.%s_session', $this->defaultSession));

        $sessions = array();
        foreach (array_keys($config['sessions']) as $name) {
            $sessions[$name] = sprintf('doctrine_phpcr.%s_session', $name);
        }
        $container->setParameter('doctrine_phpcr.sessions', $sessions);
        $container->setParameter('doctrine_phpcr.default_session', $this->defaultSession);

        $loaded = array();
        foreach ($config['sessions'] as $name => $config) {
            $type = isset($config['backend']['type']) ? $config['backend']['type'] : 'davex';
            switch ($type) {
                case 'doctrinedbal':
                case 'davex':
                    if (empty($loaded['jackalope'])) {
                        $loader->load('jackalope.xml');
                        $loaded['jackalope'] = true;
                    }
                    $this->loadJackalopeSession($name, $config, $container, $type);
                    break;
                case 'midgard':
                    if (empty($loaded['midgard'])) {
                        $loader->load('midgard.xml');
                        $loaded['midgard'] = true;

                        if (isset($config['config'])) {
                            ini_set('midgard.configuration_file', $config['config']);
                        }
                    }
                    $this->loadMidgardSession($name, $config, $container);
                    break;
                default:
                    throw new \InvalidArgumentException("You set an unsupported transport type '$type' for session '$name'");
            }
        }
    }

    private function loadJackalopeSession($name, array $config, ContainerBuilder $container, $type)
    {
        $transport = $container
            ->setDefinition(sprintf('doctrine_phpcr.jackalope.%s_transport', $name), new DefinitionDecorator('doctrine_phpcr.jackalope.transport.'.$type))
        ;

        switch ($type) {
            case 'doctrinedbal':
                if (isset($config['backend']['connection'])) {
                    $transport->replaceArgument(0, new Reference($config['backend']['connection']));
                }
                break;
            case 'davex':
                if (isset($config['backend']['url'])) {
                    $transport->replaceArgument(1, $config['backend']['url']);
                }
                break;
        }

        $container
            ->setDefinition(sprintf('doctrine_phpcr.%s_repository', $name), new DefinitionDecorator('doctrine_phpcr.jackalope.repository'))
            ->replaceArgument(1, new Reference(sprintf('doctrine_phpcr.jackalope.%s_transport', $name)))
        ;

        $container
            ->setDefinition(sprintf('doctrine_phpcr.%s_credentials', $name), new DefinitionDecorator('doctrine_phpcr.credentials'))
            ->replaceArgument(0, $config['username'])
            ->replaceArgument(1, $config['password'])
        ;

        $container
            ->setDefinition(sprintf('doctrine_phpcr.%s_session', $name), new DefinitionDecorator('doctrine_phpcr.jackalope.session'))
            ->setFactoryService(sprintf('doctrine_phpcr.%s_repository', $name))
            ->replaceArgument(0, new Reference(sprintf('doctrine_phpcr.%s_credentials', $name)))
            ->replaceArgument(1, $config['workspace'])
        ;
    }

    private function loadMidgardSession($name, array $config, ContainerBuilder $container)
    {
        $container
            ->setDefinition(sprintf('doctrine_phpcr.%s_credentials', $name), new DefinitionDecorator('doctrine_phpcr.credentials'))
            ->replaceArgument(0, $config['username'])
            ->replaceArgument(1, $config['password'])
        ;

        $container
            ->setDefinition(sprintf('doctrine_phpcr.%s_session', $name), new DefinitionDecorator('doctrine_phpcr.midgard.session'))
            ->replaceArgument(0, new Reference(sprintf('doctrine_phpcr.%s_credentials', $name)))
            ->replaceArgument(1, $config['workspace'])
        ;
    }

    private function odmLoad($config, $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('odm.xml');

        $this->documentManagers = array();
        foreach (array_keys($config['document_managers']) as $name) {
            $this->documentManagers[$name] = sprintf('doctrine_phpcr.odm.%s_document_manager', $name);
        }
        $container->setParameter('doctrine_phpcr.document_managers', $this->documentManagers);

        if (empty($config['default_document_manager'])) {
            $tmp = array_keys($this->documentManagers);
            $config['default_document_manager'] = reset($tmp);
        }
        $container->setParameter('doctrine_phpcr.default_document_manager', $config['default_document_manager']);

        $container->setAlias('doctrine_phpcr.odm.document_manager', sprintf('doctrine_phpcr.odm.%s_document_manager', $config['default_document_manager']));

        foreach ($config['document_managers'] as $name => $documentManager) {
            $documentManager['name'] = $name;
            $this->loadOdmDocumentManager($documentManager, $container);
        }
    }

    private function loadOdmDocumentManager($documentManager, ContainerBuilder $container)
    {
        if ($documentManager['auto_mapping'] && count($this->documentManagers) > 1) {
            throw new \LogicException('You cannot enable "auto_mapping" when several PHPCR document managers are defined.');
        }

        $odmConfigDef = $container->setDefinition(sprintf('doctrine_phpcr.odm.%s_configuration', $documentManager['name']), new DefinitionDecorator('doctrine_phpcr.odm.configuration'));

        $this->loadOdmDocumentManagerMappingInformation($documentManager, $odmConfigDef, $container);

        $methods = array(
            'setMetadataDriverImpl' => new Reference('doctrine_phpcr.odm.'.$documentManager['name'].'_metadata_driver'),
        );
        foreach ($methods as $method => $arg) {
            $odmConfigDef->addMethodCall($method, array($arg));
        }
        
        if (!isset($documentManager['session'])) {
            $documentManager['session'] = $this->defaultSession;
        }

        $container->setDefinition(sprintf('doctrine_phpcr.odm.%s_session.event_manager', $documentManager['name']), new DefinitionDecorator('doctrine_phpcr.odm.document_manager.event_manager'));

        $container
            ->setDefinition(sprintf('doctrine_phpcr.odm.%s_document_manager', $documentManager['name']), new DefinitionDecorator('doctrine_phpcr.odm.document_manager.abstract'))
            ->setArguments(array(
                new Reference(sprintf('doctrine_phpcr.%s_session', $documentManager['session'])),
                new Reference(sprintf('doctrine_phpcr.odm.%s_configuration', $documentManager['name'])),
                new Reference(sprintf('doctrine_phpcr.odm.%s_session.event_manager', $documentManager['name']))
            ))
        ;
    }
    
    protected function getMappingDriverBundleConfigDefaults(array $bundleConfig, \ReflectionClass $bundle, ContainerBuilder $container)
    {        
        $this->bundleDirs[] = dirname($bundle->getFileName());
        
        return parent::getMappingDriverBundleConfigDefaults($bundleConfig, $bundle, $container);
    }

    protected function loadOdmDocumentManagerMappingInformation(array $documentManager, Definition $odmConfig, ContainerBuilder $container)
    {
        // reset state of drivers and alias map. They are only used by this methods and children.
        $this->drivers = array();
        $this->aliasMap = array();
        $this->bundleDirs = array();

        $this->loadMappingInformation($documentManager, $container);
        $this->registerMappingDrivers($documentManager, $container);

        $odmConfig->addMethodCall('setDocumentNamespaces', array($this->aliasMap));
    }

    protected function getObjectManagerElementName($name)
    {
        return 'doctrine_phpcr.odm.'.$name;
    }

    protected function getMappingObjectDefaultName()
    {
        return 'Document';
    }

    protected function getMappingResourceConfigDirectory()
    {
        return 'Resources/config/doctrine';
    }

    protected function getMappingResourceExtension()
    {
        return 'doctrine_phpcr';
    }
}
