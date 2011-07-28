<?php
namespace Symfony\Cmf\Bundle\NavigationBundle\Service;

use PHPCR\ItemInterface;
use PHPCR\NodeInterface;

use Doctrine\ODM\PHPCR\DocumentManager;

use Symfony\Cmf\Bundle\CoreBundle\Helper\PathMapperInterface;

/**
 * visitor to collect entries for a menu hierarchy
 *
 * this visitor collects entries into a liniear list. to get the hierarchy,
 * iterate over each level and have a MenuCollectorVisitor visit all children
 * of each node, aggregate the results into nested arrays.
 *
 * @author David Buchmann <david@liip.ch>
 */
class MenuCollectorVisitor extends AttributeCollectorVisitor
{
    protected $selectedurl;

    /**
     * @param DocumentManager $odm the doctrine phpcr-odm manager to get documents for the navigation nodes
     * @param PathMapperInterface $mapper to map urls to storage ids
     * @param string $selectedurl the url to the currently opened menu item to see whether a node is ancestor of that node
     */
    public function __construct(DocumentManager $odm, PathMapperInterface $mapper, $selectedurl)
    {
        parent::__construct($odm, $mapper);
        $this->selectedurl = $selectedurl;
    }

    /**
     * get information from this item.
     *
     * we expect a node, will throw an exception if anything else
     *
     * extract url, title and info whether selected into array.
     * selected is determined as: the node url is a prefix of the selectedurl.
     * TODO: is the definition of selected as being part of the url a simplified assumption? should we rather let the mapper decide?
     */
    public function visit(ItemInterface $item)
    {
        if (! $item instanceof NodeInterface) {
            throw new \Exception('Internal error: did not expect to visit a non-node object: '.get_class($item));
        }
        $document = $this->odm->find(null, $item->getPath());

        if (! $document->getVisible()) {
            // ignore hidden entries in menu
            return;
        }

        $url = $this->getUrl($document);
        $urlforhighlighting = $this->mapper->getUrl($document->getPath());
        $title = $document->getLabel();
        $info = $document->getInfo();

        $selected = (strncmp($urlforhighlighting,
                             $this->selectedurl,
                            strlen($urlforhighlighting)
                    ) === 0); // fixme: what about /bla versus /blabla ?

        $this->tree[$url] = array('url' => $url,
                                  'title' => $title,
                                  'info' => $info,
                                  'selected' => $selected,
                                  'node' => $item,
                                  'document' => $document);
    }
}
