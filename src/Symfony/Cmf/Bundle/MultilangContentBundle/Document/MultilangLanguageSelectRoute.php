<?php

namespace Symfony\Cmf\Bundle\MultilangContentBundle\Document;

use Symfony\Cmf\Bundle\ChainRoutingBundle\Document\Route;
use Symfony\Cmf\Bundle\ChainRoutingBundle\Routing\RouteAwareInterface;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Knp\Menu\NodeInterface;

/**
 * Route to use in multilanguage sites when the language is not known (e.g.
 * for /)
 *
 * The children of this route must carry all locales that should be available.
 *
 * @PHPCRODM\Document(repositoryClass="Symfony\Cmf\Bundle\ChainRoutingBundle\Document\RouteRepository")
 */
class MultilangLanguageSelectRoute extends Route implements RouteAwareInterface
{
    /** @PHPCRODM\Children */
    protected $routes;

    /**
     * Default the controller to explicitly reference the LanguageSelectorController
     *
     */
    public function __construct()
    {
        $this->setController('symfony_cmf_multilang_content.languageSelectorController:defaultLanguageAction');
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
        return $this;
    }
}
