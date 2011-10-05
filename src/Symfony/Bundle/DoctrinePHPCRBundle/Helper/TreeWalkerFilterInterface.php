<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\Helper;

use PHPCR\ItemInterface;

/**
 * @author Daniel Barsotti <daniel.barsotti@liip.ch>
 */
interface TreeWalkerFilterInterface
{
    public function mustVisit(ItemInterface $node);
}
