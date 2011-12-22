<?php

namespace Symfony\Cmf\Bundle\MenuBundle;

use Knp\Menu\Silex\RouterAwareFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Cmf\Bundle\ChainRoutingBundle\Routing\DoctrineRouter;

class ContentAwareFactory extends RouterAwareFactory
{
    protected $content_router = null;

    /**
     * @param UrlGeneratorInterface $generator for the parent class
     * @param DoctrineRouter $content_router to generate routes when content is set
     * @param Request $request to determine whether this is the current menu item
     */
    public function __construct(UrlGeneratorInterface $generator, DoctrineRouter $content_router, Request $request)
    {
        parent::__construct($generator);
        $this->content_router = $content_router;
        $this->request = $request;
    }

    public function createItem($name, array $options = array())
    {
        $current = false;
        if (!empty($options['content'])) {
            if ($this->request->attributes->get(DoctrineRouter::CONTENT_KEY) == $options['content']) {
                $current = true;
            }
            $options['uri'] = $this->content_router->generate(null, $options);
            unset($options['route']);
        }

        $item = parent::createItem($name, $options);
        if ($current) {
            $item->setCurrent(true);
        }
        return $item;
    }
}
