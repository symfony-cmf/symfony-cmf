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

        foreach ($root->getNodes('*') as $name => $node) {
            $child = $this->nodeToArray($name, $node);

            foreach ($node->getNodes('*') as $name => $grandson) {
                $child['children'][] = $this->nodeToArray($name, $grandson);
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

    public function move($moved_path, $target_path)
    {
        $resulting_path = $target_path.'/'.basename($moved_path);
        
        $workspace = $this->session->getWorkspace();
        $workspace->move($moved_path, $target_path.'/'.basename($moved_path));
        
        return $resulting_path;
    }

    private function nodeToArray($name, $node)
    {
        $has_children = (bool)count($node->getNodes('*'));
        return array(
            'data'  => $name,
            'attr'  => array(
                'id' => $node->getPath(),
                'rel' => $has_children ? 'folder' : 'default',
                'classname' => $node->hasProperty('phpcr:class') ? $node->getProperty('phpcr:class')->getString() : null
            ),
            'state' => $has_children ? 'closed' : null,
        );
    }
    
}
