<?php

namespace Symfony\Cmf\Bundle\PHPCRBrowserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Cmf\Bundle\PHPCRBrowserBundle\Tree\TreeInterface;

/**
 * @author Jacopo Jakuza Romei <jromei@gmail.com>
 * @author Lukas Kahwe Smith <smith@pooteeweet.org>
 */
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
    protected function processNode($path, $method)
    {
        if (empty($path)) {
            $path = '/';
        }

        return new Response(json_encode($this->tree->$method($path)));
    }

    public function childrenAction(Request $request)
    {
        $path = $request->query->get('root');
        return $this->processNode($path, "getChildren");
    }

    public function propertiesAction(Request $request)
    {
        $path = $request->query->get('root');
        return $this->processNode($path, "getProperties");
    }

    public function moveAction(Request $request)
    {
        $moved = $request->request->get('dropped');
        $target = $request->request->get('target');

        return new Response($this->tree->move($moved, $target));
    }
}
