<?php
namespace Symfony\Cmf\Bundle\MenuBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

class SymfonyCmfMenuExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('phpcr-menu.xml');

        $container->setParameter('symfony_cmf_menu.menu_basepath', $config['menu_basepath']);
        $container->setParameter('symfony_cmf_menu.document_manager', $config['document_manager']);
    }
}
