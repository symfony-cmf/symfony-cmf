<?php

namespace Symfony\Cmf\Bundle\PHPCRBrowserBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Cmf\Bundle\PHPCRBrowserBundle\Tree\TreeInterface;

class PHPCRBrowserController
{
    /**
     * @var PHPCR\SessionInterface
     */
    protected $tree;

    public function __construct(TreeInterface $tree)
    {
        $this->tree = $tree;
    }

    /**
     * @param type $root Node to process
     * @param type $method Method to execute on the node
     * @return \Symfony\Component\HttpFoundation\Response 
     */
    private function processNode($root, $method)
    {
        $path = $root !== 'source' ? $root : '/';

        return new Response($this->tree->$method($path));
    }
    
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return array("foo" => 1);
    }

    /**
     * @Route("/children", defaults={"root" = "source"})
     * @Route("/children/{root}")
     * @Template()
     */
    public function childrenAction($root)
    {
        return $this->processNode($root, "getJSONChildren");
    }

    /**
     * @Route("/properties", defaults={"root" = "source"})
     * @Route("/properties/{root}")
     * @Template()
     */
    public function propertiesAction($root)
    {
        return $this->processNode($root, "getJSONProperties");
    }
}
