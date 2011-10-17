<?php

namespace Symfony\Cmf\Bundle\NavigationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * A controller to render navigational elements into simple html that can be
 * styled further
 *
 * If you have more than one menu, you will need different routes to different
 * instances of a navigation controller. The urls must be only the part after
 * such information
 */
class NavigationRendererController
{
    protected $templating;
    protected $walker;
    protected $routename;

    /**
     * @param HierarchyWalker $walker the service to know about hierarchies
     */
    public function __construct(EngineInterface $templating, $walker, $routename)
    {
        $this->templating = $templating;
        $this->walker = $walker;
        $this->routename = $routename;
    }

    /**
     * make sure the url is valid
     *
     * TODO: this should be improved
     */
    protected function checkUrl($url)
    {
        if (strstr($url, '/../')) {
            throw new \Exception('Invalid url');
        }
    }

    /**
     * Render the list of child nodes of an url (linking titles to the urls)
     *
     * @param string url the url you want the children for
     */
    public function childlistAction($url)
    {
        $this->checkUrl($url);
        $children = $this->walker->getChildList($url);
        return $this->templating->renderResponse('SymfonyCmfNavigationBundle:NavigationRenderer:childlist.html.twig',
                             array('children' => $children,
                                   'routename' => $this->routename));
    }

    /**
     * Render a breadcrumb to an url. Each item will link to its url.
     *
     * @param string url the url you want the breadcrumb to
     */
    public function breadcrumbAction($url)
    {
        $this->checkUrl($url);
        $breadcrumb = $this->walker->getAncestors($url);
        return $this->templating->renderResponse('SymfonyCmfNavigationBundle:NavigationRenderer:breadcrumb.html.twig',
                             array('breadcrumb' => $breadcrumb,
                                   'routename' => $this->routename));
    }

    /**
     * Render a menu open at a url. Each item will link to its url.
     *
     * See HierarchyWalker::getMenu for documentation on the array structure you get for the template
     *
     * @param string url the url to the currently open item
     */
    public function menuAction($url)
    {
        $this->checkUrl($url);
        $menu = $this->walker->getMenu($url);

        // would be nicer if we could get that information from the walker...
        $entryselected = false;
        foreach ($menu as $entry) {
            if ($entry['selected']) {
                $entryselected = true;
            }
        }

        return $this->templating->renderResponse('SymfonyCmfNavigationBundle:NavigationRenderer:menu.html.twig',
                             array('root' => $menu,
                                   'routename' => $this->routename,
                                   'entryselected' => $entryselected,
                             ));
    }

    public function sitemapAction()
    {
        $map = $this->walker->getMenu('/', -1);
        return $this->templating->renderResponse('SymfonyCmfNavigationBundle:NavigationRenderer:sitemap.html.twig',
                             array('map' => $map, 'routename' => $this->routename));
    }
}
