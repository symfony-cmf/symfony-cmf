<?php

/*
 * This file is part of the Symfony/Cmf/PhpcrCommandsBundle
 *
 * (c) Daniel Barsotti <daniel.barsotti@liip.ch>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\Cmf\Bundle\PhpcrCommandsBundle\Helper;

use Symfony\Bundle\DoctrinePHPCRBundle\JackalopeLoader;
use Jackalope\Node;
use Jackalope\Property;
use Jackalope\Transport\Davex\HTTPErrorException;

/**
 * Helper class to manipulate PHPCR nodes
 */
class NodeHelper
{
    /**
     * @var JackalopeLoader
     */
    protected $session;

    /**
     * @var Node
     */
    protected $root;

    /**
     * @param JackalopeLoader $loader
     */
    public function __construct(JackalopeLoader $loader)
    {
        $this->session = $loader->getSession();
        $this->root = $this->session->getRootNode();
    }

    /**
     * Return the jackalope session object
     *
     * @return Jackalope\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Return the root node
     *
     * @return Jackalope\Node
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Get a node given its path. Return false if none found.
     *
     * @param string $path Path of the node
     */
    public function getNode($path)
    {
        try {

            $node = $this->session->getNode($path);
            return $node;

        } catch (\PHPCR\PathNotFoundException $ex) {

            return false;
        }
    }

    /**
     * Create a new node with the given name as a child of $parent node.
     * If $parent is null then the new node is create at the root.
     *
     * @param string $name Name of the new node
     * @param Node $parent Parent node of the new node
     * @return Node
     */
    public function createNode($name, Node $parent = null)
    {
        if ($parent === null) {
            $parent = $this->root;
        }

        try {

            $node = $parent->addNode($name);
            $this->session->save();

        } catch (HTTPErrorException $ex) {

            return false;
        }

        return $node;
    }

    /**
     * Create a node and it's parents, if necessary.  Like mkdir -p.
     *
     * @param string $path  full path, like /content/jobs/data
     * @return Node
     */
    public function createPath($path)
    {
        $current = $this->root;

        $segments = preg_split('#/#', $path, null, PREG_SPLIT_NO_EMPTY);
        try {
            foreach ($segments as $segment) {
                if ($current->hasNode($segment)) {
                    $current = $current->getNode($segment);
                } else {
                    $current = $current->addNode($segment);
                }
            }
            $this->session->save();
        } catch (HTTPErrorException $ex) {
            return false;
        }

        return $current;
    }

    /**
     * Delete all the nodes in the repository which are not prefixed with jcr:
     */
    public function deleteAllNodes()
    {
        foreach($this->root->getNodes() as $node) {
            if (! $this->isSystemNode($node)) {
                $node->remove();
            }
        }
        $this->session->save();
    }

    public function isSystemNode(Node $node)
    {
        return substr($node->getName(), 0, 4) === 'jcr:';
    }

    public function isSystemProperty(Property $prop)
    {
        return substr($prop->getName(), 0, 4) === 'jcr:';
    }



}
