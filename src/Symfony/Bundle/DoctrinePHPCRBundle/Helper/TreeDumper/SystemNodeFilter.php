<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\Helper\TreeDumper;

use PHPCR\ItemInterface;
use Symfony\Bundle\DoctrinePHPCRBundle\Helper\TreeWalkerFilterInterface;

/**
 * Filter system properties and nodes based on their name.
 * 
 * @author Daniel Barsotti <daniel.barsotti@liip.ch>
 */
class SystemNodeFilter implements TreeWalkerFilterInterface
{
    public function mustVisit(ItemInterface $node)
    {
        return substr($node->getName(), 0, 4) !== 'jcr:';
    }
}
