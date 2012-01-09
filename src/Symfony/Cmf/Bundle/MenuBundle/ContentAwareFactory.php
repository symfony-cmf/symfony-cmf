<?php

namespace Symfony\Cmf\Bundle\MenuBundle;

use Knp\Menu\Silex\RouterAwareFactory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\Container;

use Symfony\Cmf\Bundle\ChainRoutingBundle\Routing\DoctrineRouter;

class ContentAwareFactory extends RouterAwareFactory
{
    protected $contentRouter;
    protected $container;

    /**
     * @param UrlGeneratorInterface $generator for the parent class
     * @param DoctrineRouter $contentRouter to generate routes when content is set
     * @param Container $container to fetch the request in order to determine whether this is the current menu item
     */
    public function __construct(UrlGeneratorInterface $generator, DoctrineRouter $contentRouter, Container $container)
    {
        parent::__construct($generator);
        $this->contentRouter = $contentRouter;
        $this->container = $container;
    }

    public function createItem($name, array $options = array())
    {
        $current = false;
        if (!empty($options['content'])) {
            try {
                $request = $this->container->get('request');
                if ($request->attributes->get(DoctrineRouter::CONTENT_KEY) == $options['content']) {
                    $current = true;
                }
            } catch (\Exception $e) {}

            $options['uri'] = $this->contentRouter->generate(null, $options);
            unset($options['route']);
        }

        $item = parent::createItem($name, $options);
        if ($current) {
            $item->setCurrent(true);
        }

        return $item;
    }
}
