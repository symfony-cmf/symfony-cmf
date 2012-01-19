<?php

namespace Symfony\Cmf\Bundle\MultilangContentBundle\Document;

use Symfony\Cmf\Bundle\ChainRoutingBundle\Routing\RouteObjectInterface;
use Symfony\Cmf\Bundle\ChainRoutingBundle\Routing\RouteAwareInterface;
use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Knp\Menu\NodeInterface;

/**
 * Route to use in multilanguage sites when the language is not known (e.g.
 * for /)
 *
 * The children of this route must carry all locales that should be available.
 *
 * @PHPCRODM\Document
 */
class MultilangLanguageSelectRoute implements RouteObjectInterface, RouteAwareInterface
{
    /**
     * @PHPCRODM\ParentDocument
     */
    protected $parent;
    /**
     * @PHPCRODM\Nodename
     */
    protected $name;

    /**
     * The full repository path to this route object
     * TODO: the strategy=parent argument should not be needed, we do have a ParentDocument annotation
     * @PHPCRODM\Id(strategy="parent")
     */
    protected $path;

    /** @PHPCRODM\Children */
    protected $routes;

    /**
     * Explicit controller to be used. Defaults to the
     * LanguageSelectorController
     *
     * @PHPCRODM\String()
     */
    protected $controller = 'symfony_cmf_multilang_content.languageSelectorController:defaultLanguageAction';

    /**
     * Set the parent document and name of this route entry. Only allowed when
     * creating a new item!
     *
     * The url will be the url of the parent plus the supplied name.
     */
    public function setPosition($parent, $name)
    {
        $this->parent = $parent;
        $this->name = $name;
    }

    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the explicit controller to be used with this route.
     * i.e. service_name:indexAction or MyBundle:Default:index
     *
     * @param string $controller the controller to be used with this route
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * Get the explicit controller to be used with this route
     *
     * @return string the controller name or service name with action
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return array of route objects that point to this content
     */
    public function getRoutes()
    {
        if (is_array($this->routes)) {
            return $this->routes;
        }
        return $this->routes->toArray();
    }

    public function getRouteContent()
    {
        return null;
    }

    public function getRouteDefaults()
    {
        $defaults = array('route' => $this);

        $controller = $this->getController();
        if (! empty($controller)) {
            $defaults['_controller'] = $controller;
        }
        return $defaults;
    }
}
