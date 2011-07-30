<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Provider;

use Knp\Bundle\MenuBundle\ProviderInterface;
use Knp\Bundle\MenuBundle\MenuItem;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ODM\PHPCR\DocumentManager;

class PHPCRMenuProvider implements ProviderInterface
{
    protected $menu_root = null;
    protected $container = null;
    protected $dm = null;
    protected $router = null;

    public function __construct(ContainerInterface $container, $dm_name, $menu_root)
    {
        $this->container = $container;
        $this->dm = $this->container->get($dm_name);
        $this->menu_root = $menu_root;
        $this->router = $this->container->get('router');
    }

    public function getMenu($name)
    {
        $menu = $this->dm->find(null, $this->menu_root . '/' . $name);
        return $this->createFromMenu($menu);
    }

    protected function createFromMenu($menu)
    {
        $item = new MenuItem($menu->getName(), $this->getUri($menu), $menu->getAttributes());
        $item->setLabel($menu->getLabel());


        foreach ($menu->getChildren() as $childMenu) {
            $item->addChild($this->createFromMenu($childMenu));
        }

        return $item;
    }

    protected function getUri($menu)
    {
        if ($menu->getUri() !== null) {
            return $menu->getUri();
        } else if ($menu->getRoute() !== null) {
            return $this->router->generate($menu->getRoute());
        }
        return '';
    }

    protected function determineCurrentMenu($menu)
    {
        if (false) {
            $item->setIsCurrent($this->determineCurrentMenu($menu));
        }
    }
}
