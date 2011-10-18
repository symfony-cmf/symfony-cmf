<?php

namespace Symfony\Cmf\Bundle\MultilangContentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

/**
 * Dependency injection class to load services.xml
 *
 * @author brian.king (at) liip.ch
 */
class SymfonyCmfMultilangContentExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // TODO: use the configuration mechanism to validate the configuration

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('config.xml');

        $config = array();
        foreach ($configs as $conf) {
            $config = array_replace($config, $conf);
        }

        $alias = $this->getAlias();
        foreach ($config as $key => $value) {
            $container->setParameter($alias . '.' . $key, $value);
        }

        $loader->load('services.xml');
    }
}
