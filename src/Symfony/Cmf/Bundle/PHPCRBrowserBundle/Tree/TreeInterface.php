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
     * Returns an array representation of children nodes of a node
     * 
     * @param string $path The path of any PHPCR node
     * @return array children list
     */
    function getChildren($path);
    
    /**
     * Returns an array representation of properties of a node
     * 
     * @param string $path The path of any PHPCR node
     * @return array properties list
     */
    function getProperties($path);
}

