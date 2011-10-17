<?php
namespace Symfony\Cmf\Bundle\NavigationBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class SymfonyCmfNavigationExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // TODO move this to the Configuration class as soon as it supports setting such a default
        array_unshift($configs, array(
            'controllers_by_alias' => array(
                'static_pages' => 'symfony_cmf_content.controller:indexAction',
            ),
            'controllers_by_content' => array(
                'Symfony\Cmf\Bundle\ContentBundle\Document\StaticContent' => 'symfony_cmf_content.controller:indexAction',
            ),
        ));

        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $container->setParameter("symfony_cmf_navigation.controllers_by_alias", $config['controllers_by_alias']);
        $container->setParameter("symfony_cmf_navigation.controllers_by_content", $config['controllers_by_content']);
        $container->setParameter("symfony_cmf_navigation.mainmenu_routename", $config['mainmenu_routename']);
        $container->setParameter("symfony_cmf_navigation.document", $config['document']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
