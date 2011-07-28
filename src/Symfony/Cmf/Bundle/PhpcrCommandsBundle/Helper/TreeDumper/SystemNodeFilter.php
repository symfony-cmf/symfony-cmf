<?php

/*
 * This file is part of the Symfony/Cmf/PhpcrCommandsBundle
 *
 * (c) Daniel Barsotti <daniel.barsotti@liip.ch>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\Cmf\Bundle\PhpcrCommandsBundle\Helper\TreeDumper;

use PHPCR\ItemInterface;
use Symfony\Cmf\Bundle\PhpcrCommandsBundle\Helper\TreeWalkerFilterInterface;

/**
 * Filter system properties and nodes based on their name.
 */
class SystemNodeFilter implements TreeWalkerFilterInterface
{
    public function must_visit(ItemInterface $node)
    {
        return substr($node->getName(), 0, 4) !== 'jcr:';
    }
}