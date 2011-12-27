<?php

namespace Symfony\Cmf\Bundle\PHPCRBrowserBundle\Tree;

use PHPCR\SessionInterface;

/**
 * A simple class to get PHPCR trees in JSON format
 *
 * @author Jacopo 'Jakuza' Romei <jromei@gmail.com>
 * @author Lukas Kahwe Smith <smith@pooteeweet.org>
 */
class PHPCRTree implements TreeInterface
{
    private $session;
    
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    
    public function getChildren($path)
    {
        $root = $this->session->getNode($path);

        $children = array();

        foreach ($root->getNodes() as $name => $node) {
            $child = array(
                'data'  => $name,
                'attr'  => array('id' => $node->getPath()),
                'state' => 'closed'
            );

            foreach ($node->getNodes('*') as $name => $grandson) {
                $child['children'][] = array(
                    'data' => $name, 
                    'attr'  => array('id' => $grandson->getPath()),
                    'state' => count($grandson->getNodes('*')) ? 'closed' : 'open');
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
