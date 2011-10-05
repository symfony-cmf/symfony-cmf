<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\Helper\Fixtures;

use Doctrine\ODM\PHPCR\DocumentManager;
use Doctrine\Common\DataFixtures\Executor\AbstractExecutor;
use Symfony\Bundle\DoctrinePHPCRBundle\Helper\Fixtures\PHPCRPurger;

/**
 * Class responsible for executing data fixtures.
 *
 * @author Jonathan H. Wage <jonwage@gmail.com>
 * @author Daniel Barsotti <daniel.barsotti@liip.ch>
 */
class PHPCRExecutor extends AbstractExecutor
{
    /**
     * Construct new fixtures loader instance.
     *
     * @param DocumentManager $dm DocumentManager instance used for persistence.
     */
    public function __construct(DocumentManager $dm, PHPCRPurger $purger = null)
    {
        $this->dm = $dm;
        if ($purger !== null) {
            $this->purger = $purger;
            $this->purger->setDocumentManager($dm);
        }
        parent::__construct($dm);
    }

    /** @inheritDoc */
    public function execute(array $fixtures, $append = false)
    {
        if ($append === false) {
            $this->purge();
        }
        foreach ($fixtures as $fixture) {
            $this->load($this->dm, $fixture);
        }
    }
}

