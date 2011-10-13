<?php

namespace Symfony\Cmf\Bundle\PHPCRBrowserBundle\Tree;

/**
 * The Tree interface describes how a class feeding data for a tree representation of a PHPCR repository shall look like.
 * 
 * @author Jacopo Jakuza Romei <jromei@gmail.com>
 * @author cirpo <alessandro.cinelli@gmail.com>
*/
interface TreeInterface
{
    /**
     * Returns a JSON representation of children nodes of a node
     * 
     * @param string $path The path of any PHPCR node
     * @return string JSON formatted children list
     */
    public function getJSONChildren($path);
    
    /**
     * Returns a JSON representation of properties of a node
     * 
     * @param string $path The path of any PHPCR node
     * @return string JSON formatted properties list
     */
    public function getJSONProperties($path);
}

