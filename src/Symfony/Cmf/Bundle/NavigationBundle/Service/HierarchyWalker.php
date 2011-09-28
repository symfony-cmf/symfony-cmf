<?php
namespace Symfony\Cmf\Bundle\NavigationBundle\Service;

use PHPCR\SessionInterface;
use PHPCR\NodeInterface;
use PHPCR\ItemVisitorInterface;

use Doctrine\ODM\PHPCR\DocumentManager;

use Symfony\Cmf\Bundle\CoreBundle\Helper\PathMapperInterface;

/**
 * this service knows about phpcr and builds navigation information
 *
 * each method exists in getX form, where it returns arrays and in visit form
 * that allows to pass your own visitor.
 *
 * the visitMenu visitor has to fulfill some requirements for the walker to
 * work properly, all other visitors just need to implement the standard
 * PHPCR\ItemVisitorInterface
 *
 * Security: Be careful not to pass paths with ../. If you do, you might expose
 * things you do not want to expose, or the service could be confused and throw
 * an error
 *
 * @author David Buchmann <david@liip.ch>
 */
class HierarchyWalker
{
    /**
     * @var DocumentManager
     */
    protected $odm;

    /**
     * @var PHPCR\SessionInterface
     */
    protected $session;

    /**
     * @var Symfony\Cmf\Bundle\CoreBundle\Helper\PathMapperInterface
     */
    protected $mapper;

    /**
     * node object that is the root of this navigation tree
     * not to be confused with the repository root
     * @var PHPCR\NodeInterface
     */
    protected $rootnode;

    /**
     * @param DocumentManager $document_manager the phpcr-odm document manager to load navigation entry documents
     * @param \PHPCR\SessionInterface $session the phpcr session. TODO: remove when all annotations work
     * @param PathMapperInterface $mapper to map urls to storage ids
     */
    public function __construct(DocumentManager $document_manager, $session, PathMapperInterface $mapper)
    {
        $this->odm = $document_manager;
        $this->session = $session;
        $this->mapper  = $mapper;
        $basepath = $mapper->getStorageId('/');
        $this->rootnode = $this->session->getNode($basepath);
        if ($this->rootnode == null) {
            throw new Exception("Did not find any node at $basepath");
        }
    }

    /**
     * Factory method to create the visitor to collect the child items.
     */
    protected function createChildListVisitor()
    {
        return new AttributeCollectorVisitor($this->odm, $this->mapper);
    }

    /**
     * Get the direct children of a node identified by url
     *
     * @param string $url the url (without eventual prefix from routing config)
     * @return array with url => title for each child of the node at $url
     */
    public function getChildList($url)
    {
        $visitor = $this->createChildListVisitor();
        $this->visitChildren($url, $visitor);
        return $visitor->getArray();
    }

    /**
     * Let this visitor visit all direct children of $url
     *
     * @param string $url the url (without eventual prefix from routing config)
     * @param ItemVisitorInterface $visitor the visitor to look at the nodes
     */
    public function visitChildren($url, ItemVisitorInterface $visitor)
    {
        $node = $this->session->getNode($this->mapper->getStorageId($url));
        foreach ($node as $child) {
            $child->accept($visitor);
        }
    }

    /**
     * Factory method to create the visitor to collect the ancestor items.
     */
    protected function createAncestorsVisitor()
    {
        return new AttributeCollectorVisitor($this->odm, $this->mapper);
    }

    /**
     * Get all ancestors from root node according to mapper down to the parent
     * of the node identified by url
     *
     * @param string $url the url (without eventual prefix from routing config)
     * @return array with url => title, starting with root node, ending with the parent of url
     */
    public function getAncestors($url)
    {
        $visitor = $this->createAncestorsVisitor();
        $this->visitAncestors($url, $visitor);
        return $visitor->getArray();
    }

    /**
     * Let the visitor visit the ancestors from root node according to mapper
     * down to the parent of the node identified by url
     *
     * @param string $url the url (without eventual prefix from routing config)
     * @param ItemVisitorInterface $visitor the visitor to look at the nodes
     */
    public function visitAncestors($url, ItemVisitorInterface $visitor)
    {
        $node = $this->session->getNode($this->mapper->getStorageId($url));
        $i = $this->rootnode->getDepth();
        while(($ancestor = $node->getAncestor($i++)) != $node) {
            $ancestor->accept($visitor);
        }
    }

    /**
     * Factory method to create the visitor to collect menu items.
     *
     * Extend HierarchyWalker to create a different visitor, or use visitMenu
     * with your own visitor.
     *
     * In addition to implement PHPCR\ItemVisitorInterface, the visitor must
     * have the getArray and reset methods
     * getArray returns information about each visited node in the format
     *   explained at getMenu
     * reset sets the internal information back to empty, so that the visitor
     *   can be reused for the next navigation level
     *
     * @param string $url the url to the selected path
     */
    protected function createMenuVisitor($url)
    {
        return new MenuCollectorVisitor($this->odm, $this->mapper, $url);
    }

    /**
     * Build a menu tree leading to this path.
     *
     * Using the depth parameter, you can load more than the nodes in selected
     * path and their siblings,
     * i.e. to preload children of other menu items or to build a sitemap
     *
     * The structure is a nested array of arrays with the navigation root as
     * first array.
     *
     * array("url" => "/",
     *       "title" => "X",
     *       "selected" => true, #whether this entry is in the selected path
     *       "node" => [PHPCRNode Object],
     *       "children" => array("/x" => array([node x with maybe children]),
     *                           "/y" => array([node y with maybe children]),
     *                          )
     * );
     * If skiproot is true (the default) the top structure is an array of
     * children instead.
     *
     *
     * TODO: is the definition of selected as being part of the url a simplified assumption? should we rather let the mapper decide?
     *
     * @param string $path the url (without eventual prefix from routing config)
     * @param bool $skiproot whether to not include the root node in the collection, defaults to skipping it
     * @param int $depth depth to follow unselected node children. defaults to 0 (do not follow). -1 means unlimited
     *
     * @return array structure with entries for each node: title, url,
     *    selected (parent of $url or $url itselves), node (the phpcr node),
     *    children (array, empty array on no children. false if not selected
     *    node and deeper away from selected node than depth.). if you skip
     *    the root, the uppermost thing is directly an array of children
     */
    public function getMenu($path, $skiproot = true, $depth=0)
    {
        $visitor = $this->createMenuVisitor($path);
        return $this->visitMenu($path, $visitor, $skiproot, $depth);
    }

    /**
     * Visit the menu tree leading to this path with a specified visitor.
     *
     * Implementation: the visitor is reset after each child list
     *
     * @see getMenu()
     * @param string $path the url (without eventual prefix from routing config)
     * @param ItemVisitorInterface $visitor to gather information from a node, with the same behaviour and additional methods as explained in createMenuVisitor()
     * @param bool $skiproot whether to not include the root node in the collection, defaults to skipping it
     * @param int $depth depth to follow unselected node children. defaults to 0 (do not follow). -1 means unlimited
     *
     * @return array structure with entries for each node: title, url, selected (parent of $url or $url itselves), node (the phpcr node), children (array, empty array on no children. false if not selected node and deeper away from selected node than depth.). if you skip the root, the uppermost thing is directly an array of children
     */
    public function visitMenu($path, $visitor, $skiproot = true, $depth=0)
    {
        if ($skiproot) {
            //have a fake parentrecord
            $tree = array('selected' => true, 'node' => $this->rootnode);
        } else {
            $this->rootnode->accept($visitor);
            $tree = $visitor->getArray();
            $tree = reset($tree); //visitor just was at the root node, there is exactly one
        }
        $children= $this->visitMenuRecursive($tree, $path, $visitor, $depth, 0);
        if ($skiproot) {
            $tree = $children;
        } else {
            $tree['children'] = $children;
        }
        return $tree;
    }

    /**
     * Iterate over the menu tree recursively, starting with the children of each record from the MenuCollectorVisitor
     *
     * @param array $parentrecord as returned by MenuCollectorVisitor
     * @param string $path the node path of the selected url
     * @param int $depth the depth to which to follow unselected nodes, -1 for unlimited
     * @param int $curdepth current depth recursion is into unselected nodes
     * @return nested array of all children of this node and their children down the selected path and others down to $depth
     */
    protected function visitMenuRecursive(&$parentrecord, $path, $visitor, $depth, $curdepth)
    {
        $visitor->reset();
        foreach ($parentrecord['node'] as $child) {
            //iterate over that node's children
            $child->accept($visitor);
        }
        $list = $visitor->getArray();
        $childselected = false;
        foreach ($list as $key => &$record) {
            if ($record['selected']) {
                $childselected = true;
                $list[$key]['children'] = $this->visitMenuRecursive($record, $path, $visitor, $depth, 0);
            } elseif ($curdepth < $depth) {
                $list[$key]['children'] = $this->visitMenuRecursive($record, $path, $visitor, $depth, $curdepth + 1);
            } elseif ($depth === -1) {
                $list[$key]['children'] = $this->visitMenuRecursive($record, $path, $visitor, $depth, -1);
            } else {
                $list[$key]['children'] = false;
            }
        }
        $parentrecord['childselected'] = $childselected;
        return $list;
    }
}
