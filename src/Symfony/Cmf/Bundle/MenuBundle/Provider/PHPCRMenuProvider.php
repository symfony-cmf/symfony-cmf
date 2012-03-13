<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Provider;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Knp\Menu\FactoryInterface;
use Knp\Menu\Provider\MenuProviderInterface;

class PHPCRMenuProvider implements MenuProviderInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var FactoryInterface
     */
    protected $factory = null;
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $dm;
    /**
     * base for menu ids
     * @var string
     */
    protected $menuRoot;
    /**
     * doctrine document class name
     * @var string
     */
    protected $className;


    /**
     * @param ContainerInterface $container di container to get request from to
     *      know current request uri
     * @param FactoryInterface $factory the menu factory to create the menu
     *      item with the root document (usually ContentAwareFactory)
     * @param string $objectManagerName document manager service name to load menu root
     *      document from
     * @param string $menuRoot root id of the menu
     * @param string $className the menu document class name. with phpcr-odm
     *      this can be null
     */
    public function __construct(ContainerInterface $container, FactoryInterface $factory, $objectManagerName, $menuRoot, $className=null)
    {
        $this->container = $container;
        $this->factory = $factory;
        $this->dm = $this->container->get($objectManagerName);
        $this->menuRoot = $menuRoot;
        $this->className = $className;
    }

    public function get($name, array $options = array())
    {
        $menu = $this->dm->find($this->className, $this->menuRoot . '/' . $name);
        if ($menu === null) {
            throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
        }
        $menuItem = $this->factory->createFromNode($menu);
        $menuItem->setCurrentUri($this->container->get('request')->getRequestUri());
        return $menuItem;
    }

    public function has($name, array $options = array())
    {
        $menu = $this->dm->find($this->className, $this->menuRoot . '/' . $name);
        return $menu !== null;
    }
}
