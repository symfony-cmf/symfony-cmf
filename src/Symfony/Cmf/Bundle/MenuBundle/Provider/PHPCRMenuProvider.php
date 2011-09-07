<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Provider;

use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PHPCRMenuProvider implements MenuProviderInterface
{
    protected $container = null;
    protected $dm = null;
    protected $menu_root = null;
    protected $factory = null;

    public function __construct(ContainerInterface $container, $dm_name, $menu_root)
    {
        $this->container = $container;
        $this->dm = $this->container->get($dm_name);
        $this->menu_root = $menu_root;
        $this->factory = $this->container->get('phpcr.menu.factory');
    }

    public function get($name)
    {
        $menu = $this->dm->find(null, $this->menu_root . '/' . $name);
        $menuItem = $this->factory->createFromNode($menu);
        $menuItem->setCurrentUri($this->container->get('request')->getRequestUri());
        return $menuItem;
    }

    public function has($name)
    {
        $menu = $this->dm->find(null, $this->menu_root . '/' . $name);
        return $menu !== null;
    }
}
