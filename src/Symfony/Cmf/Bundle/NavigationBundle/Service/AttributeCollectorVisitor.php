<?php
namespace Symfony\Cmf\Bundle\NavigationBundle\Service;

use PHPCR\ItemVisitorInterface;
use PHPCR\ItemInterface;
use PHPCR\NodeInterface;

use Doctrine\ODM\PHPCR\DocumentManager;

use Symfony\Cmf\Bundle\CoreBundle\Helper\PathMapperInterface;

/**
 * visitor to collect url => title into a flat array
 *
 * creates the odm documents from the nodes in order to let operations like
 * translation execute.
 *
 * @author David Buchmann <david@liip.ch>
 */
class AttributeCollectorVisitor implements ItemVisitorInterface
{
    protected $odm;
    protected $mapper;
    protected $showall;
    protected $tree = array();

    /**
     * @param DocumentManager $odm the doctrine phpcr-odm manager to get
     *      documents for the navigation nodes
     * @param PathMapperInterface $mapper to map urls to storage ids
     * @param bool $showall whether all or only visible navigation entries
     *      should be collected. defaults to false, only visible
     */
    public function __construct(DocumentManager $odm, PathMapperInterface $mapper, $showall = false)
    {
        $this->odm = $odm;
        $this->mapper = $mapper;
        $this->showall = $showall;
    }

    /**
     * as defined by interface: do something with this item.
     * we expect a node, will throw an exception if anything else
     */
    public function visit(ItemInterface $item)
    {
        if (! $item instanceof NodeInterface) {
            throw new \Exception("Internal error: did not expect to visit a non-node object: $item");
        }

        $document = $this->odm->find(null, $item->getPath());

        if (! $this->showall && ! $document->getVisible()) {
            // ignore hidden entries
            return;
        }

        $url = $this->getUrl($document);
        $this->tree[$url] = $document->getLabel(); //TODO: this could return the same list of info as menucollectorvisitor, making that one obsolete
    }

    /**
     * @return the aggregated array
     */
    public function getArray()
    {
        return $this->tree;
    }

    /**
     * reset aggregated information to empty array
     */
    public function reset()
    {
        $this->tree = array();
    }

    /**
     * Get the navigation url with the help of the mapper
     *
     * This is mainly useful because it resolves redirect paths to make the url
     * point to the target navigation item
     *
     * @return string the url for the final navigation item that does not redirect
     */
    protected function getUrl($document)
    {
        $redir = $document->getRedirectPath();
        if (! empty($redir)) {
            // TODO: detect redirect circles?
            $node = $document->getRedirect();
            return $this->getUrl($this->odm->find(null, $node->getPath()));
        }

        // TODO: add external link property to navigation document and support this
        return $this->mapper->getUrl($document->getPath());
    }
}
