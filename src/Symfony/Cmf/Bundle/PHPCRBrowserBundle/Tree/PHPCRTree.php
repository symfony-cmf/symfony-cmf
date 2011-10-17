<?php

namespace Symfony\Cmf\Bundle\PHPCRBrowserBundle\Tree;

/**
 * A simple class to get PHPCR trees in JSON format
 *
 * @author Jacopo 'Jakuza' Romei <jromei@gmail.com>
 * @author Lukas Kahwe Smith <smith@pooteeweet.org>
 */
class PHPCRTree implements TreeInterface
{
    private $session;
    
    public function __construct($session)
    {
        $this->session = $session;
    }
    
    public function getChildren($path)
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
        
        return $children;
    }

    public function getProperties($path)
    {
        $node = $this->session->getNode($path);
        $properties = array();
        
        foreach ($node->getPropertiesValues() as $name => $value) {
            $properties[] = array(
                "name" => $name,
                "value" => $value,
            );
        }
        
        return $properties;
    }
}
