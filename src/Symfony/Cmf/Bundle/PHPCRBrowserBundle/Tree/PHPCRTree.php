<?php

namespace Symfony\Cmf\Bundle\PHPCRBrowserBundle\Tree;

/**
 * A simple class to get PHPCR trees in JSON format
 *
 * @author Jacopo 'Jakuza' Romei <jromei@gmail.com>
 */
class PHPCRTree implements TreeInterface
{
    private $session;
    
    public function __construct($session)
    {
        $this->session = $session;
    }
    
    public function getJSONChildren($path)
    {
        $root = $this->session->getNode($path);

        $children = array();

        foreach ($root->getNodes() as $name => $node) {
            $child = array(
                "text"  => $name,
                "id"    => $node->getPath(),
            );

            if ($node->getNodes('*')) {
                $child['hasChildren'] = true;
            }
            $children[] = $child;
        }
        
        return json_encode($children);
    }

    public function getJSONProperties($path)
    {
        $node = $this->session->getNode($path);
        $properties = array();
        
        foreach ($node->getPropertiesValues() as $name => $value) {
            $properties[] = array(
                "name" => $name,
                "value" => $value,
            );
        }
        
        return json_encode($properties);
    }
}