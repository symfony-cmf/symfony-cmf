<?php

namespace Symfony\Cmf\Bundle\ContentBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentController extends Controller
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    /**
     * @param StaticContent $page
     * @param string $path the url path for the current navigation item
     * @param array $translationUrls urls to all language versions to pass on to twig
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($page, $path, $translationUrls)
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

        return $this->render('SymfonyCmfContentBundle:StaticContent:index.html.twig', $params);
    }
}
