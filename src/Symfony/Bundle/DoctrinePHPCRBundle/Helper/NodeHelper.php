<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\Helper;

use PHPCR\SessionInterface;
use PHPCR\NodeInterface;
use PHPCR\PropertyInterface;

/**
 * Helper class to manipulate PHPCR nodes
 * 
 * @author Daniel Barsotti <daniel.barsotti@liip.ch>
 */
class NodeHelper
{
    /**
     * @var \PHPCR\SessionInterface
     */
    protected $session;

    /**
     * @var Node
     */
    protected $root;

    /**
     * @param \PHPCR\SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
        $this->root = $this->session->getRootNode();
    }

    /**
     * Return the phpcr session object
     *
     * @return \PHPCR\SessionInterface
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Return the root node
     *
     * @return \PHPCR\NodeInterface
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
        // TODO: this method has no real value i think

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
     * @param PHPCR\NodeInterface $parent Parent node of the new node
     * @return Node
     */
    public function createNode($name, NodeInterface $parent = null)
    {
        // TODO: this method has no real value i think

        if ($parent === null) {
            $parent = $this->root;
        }

        try {
            $node = $parent->addNode($name);
            $this->session->save();
        } catch (\Exception $ex) {
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
        } catch (\Exception $ex) {
            return false;
        }

        return $current;
    }

    /**
     * Delete all the nodes in the repository which are not prefixed with jcr:
     */
    public function deleteAllNodes()
    {
        foreach ($this->root->getNodes() as $node) {
            if (! $this->isSystemNode($node)) {
                $node->remove();
            }
        }
        $this->session->save();
    }

    public function isSystemNode(NodeInterface $node)
    {
        return substr($node->getName(), 0, 4) === 'jcr:';
    }

    public function isSystemProperty(PropertyInterface $prop)
    {
        return substr($prop->getName(), 0, 4) === 'jcr:';
    }
}
