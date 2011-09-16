<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\Helper\Fixtures;

use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\Common\DataFixtures\Purger\PurgerInterface;

/**
 * Class responsible for purging databases of data before reloading data fixtures.
 *
 * @author Daniel Barsotti <daniel.barsotti@liip.ch>
 */
class PHPCRPurger implements PurgerInterface
{
    /** DocumentManager instance used for persistence. */
    private $dm;

    /**
     * Construct new purger instance.
     *
     * @param DocumentManager $dm DocumentManager instance used for persistence.
     */
    public function __construct(DocumentManager $dm = null)
    {
        $this->dm = $dm;
    }

    /**
     * Set the DocumentManager instance this purger instance should use.
     *
     * @param DocumentManager $dm
     */
    public function setDocumentManager(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /** @inheritDoc */
    public function purge()
    {
        $this->deleteAllNodes();
    }

    /**
     * Delete all the nodes in the repository which are not prefixed with jcr:
     */
    protected function deleteAllNodes()
    {
        $session = $this->dm->getPhpcrSession();
        foreach($session->getRootNode()->getNodes() as $node) {
            if (substr($node->getName(), 0, 4) !== 'jcr:') {
                $node->remove();
            }
        }
        $session->save();
    }

}
