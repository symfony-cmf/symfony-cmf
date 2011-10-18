<?php

namespace Symfony\Cmf\Bundle\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentController
{
    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    /**
     * @param StaticContent $page
     * @param string $path the url path for the current navigation item
     * @param array $translationUrls urls to all language versions to pass on to twig
     * 
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($page, $path, $translationUrls = array())
    {
        if (!$page) {
            throw new NotFoundHttpException('Content not found: ' . $path);
        }

        $params = array(
            'title' => $page->title,
            'page' => $page,
            'url' => $path,
//            'translationUrls' => $translationUrls,
        );

        return $this->templating->renderResponse('SymfonyCmfContentBundle:StaticContent:index.html.twig', $params);
    }
}
