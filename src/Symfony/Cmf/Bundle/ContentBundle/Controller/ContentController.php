<?php

namespace Symfony\Cmf\Bundle\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * The content controller is a simple controller that calls a template with
 * the specified content.
 */
class ContentController
{
    /**
     * Instantiate the content controller.
     *
     * @param EngineInterface $templating the templating instance to render the
     *      template
     * @param string $defaultTemplate default template to use in case none is
     *      specified explicitly
     */
    public function __construct(EngineInterface $templating, $defaultTemplate)
    {
        $this->templating = $templating;
        $this->defaultTemplate = $defaultTemplate;
    }

    /**
     * @param StaticContent $contentDocument
     * @param string $template the template name to be used with this content
     * @param string $path the url path for the current navigation item
     * @param array $translationUrls urls to all language versions to pass on to twig
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($contentDocument, $path, $template = null, $translationUrls = array())
    {
        if (!$contentDocument) {
            throw new NotFoundHttpException('Content not found: ' . $path);
        }
        if ($template === null) {
            $template = $this->defaultTemplate;
        }

        $params = array(
            'title' => $contentDocument->title,
            'page' => $contentDocument,
            'url' => $path,
//            'translationUrls' => $translationUrls,
        );

        return $this->templating->renderResponse($template, $params);
    }
}
