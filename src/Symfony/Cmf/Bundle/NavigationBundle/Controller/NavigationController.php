<?php

namespace Symfony\Cmf\Bundle\NavigationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Parser;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerResolver;

use Doctrine\ODM\PHPCR\DocumentManager;

use Symfony\Cmf\Bundle\CoreBundle\Helper\PathMapperInterface;

/**
 * controller for navigation items
 *
 * a navigation item is a document of type Navigation and thus has fields for
 * the controller and referenced content
 */
class NavigationController extends Controller
{
    protected $container;

    protected $dm;

    /**
     * navigation document type because repo can not auto-detect
     */
    protected $documenttype;

    protected $controllers_mapping = array();

    /**
     * name for the navigation route to build links to other languages
     */
    protected $route_name;

    protected $mapper;

    public function __construct(ContainerInterface $container,
                                DocumentManager $document_manager,
                                PathMapperInterface $mapper,
                                $route_name)
    {
        $this->container = $container;
        $this->dm = $document_manager;
        $this->route_name = $route_name;
        $this->mapper = $mapper;

        $this->documenttype = $this->container->getParameter('symfony_cmf_navigation.document');
    }

    /**
     * Gets the navigation entry and calls the controller referenced in the entry.
     *
     * That controller must expect the referenced document, the path to that document and the list of languages
     */
    public function indexAction($url = '')
    {
        // TODO: this is way to much logic for a controller
        // define services and inject them!

        $crpath = $this->mapper->getStorageId($url);
        $repo = $this->dm->getRepository($this->documenttype);
        $page = $repo->find($crpath);

        if ($page == null) {
            throw new NotFoundHttpException("There is no page at $url (internal path '$crpath')");
        }

        //is this a redirect entry?
        $redirect_path = $page->getRedirectPath();
        if (! empty($redirect_path)) {
            $redirect_url = $this->mapper->getUrl($redirect_path);
            if ($redirect_url == $url) {
                throw new \Exception("$url is redirecting to itself");
            }
            return $this->redirect($this->generateUrl($this->route_name, array('url' => $redirect_url)));
        }

        // Get the referenced node if a referenced path was provided
        $content = null;
        $referenced_node = $page->getReference();
        if (! is_null($referenced_node)) {
            $content = $this->dm->find(null, $referenced_node->getPath());
        }

        if ($page->getController()) {
            $mapping = $this->container->getParameter('symfony_cmf_navigation.controllers_by_alias');

            if (array_key_exists($page->getController(), $mapping)) {
                $controller = $this->resolveController($mapping[$page->getController()]);
            } else {
                throw new \Exception("Could not find a controller mapping for '{$page->getController()}'");
            }
        /* TODO: implement
        } elseif ($page->getTemplate()) {
            if (is_null($content)) {
                throw new \LogicException('Can not render a navigation with neither content nor controller alias');
            }
            map to template and set $template
        */
        } elseif (! is_null($content)) {
            if (array_key_exists(get_class($content), $this->controllers_by_content)) {
                list($controller, $action) = $this->controllers_by_class[get_class($content)];
            /* TODO: implement
            } elseif (array_key_exists(get_class($content), $this->templates_by_class)) {
                $template = $this->templates_by_class[get_class($content)];
            */
            }
        }

        if (! empty($controller)) {
            // Execute the referenced action on the controller
            return call_user_func($controller, $content, $url);
        }
        if (! empty($template)) {
            return $this->render($template,
                        array('cmf_pagetitle' => $content->title,
                              'cmf_document' => $content,
                              'url' => $path,
                        )
            );
        }
        if (is_null($content)) {
            throw new \Exception("No explicit controller and no referenced content at $crpath");
        }
        throw new \Exception('Could not find a controller mapping for '.get_class($content)); //TODO: and neither for node type
        //TODO: implement a default content handler that works based on annotations
    }

    /**
     * load a controller by mapping. this can only be done at this point,
     * during DI the controller might not yet be available.
     *
     * @param string $name the controller name in the format service:actionMethod
     *
     * @return array of controller class and method name suitable for call_user_func
     */
    protected function resolveController($name)
    {
        // Search for the mapped controller/action
        $parser = new ControllerNameParser($this->container->get('kernel'));
        $resolver = new ControllerResolver($this->container, $parser);

        return $resolver->getController(new Request(array(), array(), array('_controller' => $name)));
    }
}
