<?php

namespace Symfony\Cmf\Bundle\PHPCRBrowserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Cmf\Bundle\PHPCRBrowserBundle\Tree\TreeInterface;

class PHPCRBrowserController
{
    /**
     * @var TreeInterface
     */
    protected $tree;

    public function __construct(TreeInterface $tree)
    {
        $this->tree = $tree;
    }

    /**
     * @param string $path Node to process
     * @param string $method Method to execute on the node
     * @return \Symfony\Component\HttpFoundation\Response 
     */
    private function processNode($path, $method)
    {
        if (empty($path)) {
            $path = '/';
        }

        return new Response($this->tree->$method($path));
    }

    public function childrenAction(Request $request)
    {
        $path = $request->query->get('root');
        return $this->processNode($path, "getJSONChildren");
    }

    public function propertiesAction(Request $request)
    {
        $path = $request->query->get('root');
        return $this->processNode($path, "getJSONProperties");
    }
}
