<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Provider;

use Knp\Bundle\MenuBundle\ProviderInterface;
use Knp\Bundle\MenuBundle\MenuItem;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ODM\PHPCR\DocumentManager;

class PHPCRMenuProvider implements ProviderInterface
{
    private $menu_root = null;
    private $container = null;
    private $dm = null;

    public function __construct(ContainerInterface $container, $dm_name, $menu_root)
    {
        $this->container = $container;
        $this->dm = $this->container->get($dm_name);
        $this->menu_root = $menu_root;
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
