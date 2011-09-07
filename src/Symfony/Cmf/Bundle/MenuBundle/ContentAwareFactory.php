<?php

namespace Symfony\Cmf\Bundle\MenuBundle;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Knp\Menu\Silex\RouterAwareFactory;

class ContentAwareFactory extends RouterAwareFactory
{
    protected $content_router = null;

    public function __construct(UrlGeneratorInterface $generator, $content_router)
    {
        parent::__construct($generator);
        $this->content_router = $content_router;
    }

    public function createItem($name, array $options = array())
    {
        if (!empty($options['content'])) {
            $options['uri'] = $this->content_router->generate($options['content']);
        }

        return parent::createItem($name, $options);
    }
}
