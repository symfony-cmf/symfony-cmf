<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Doctrine\ODM\PHPCR\ChildrenCollection;

/**
 * Block that contains other blocks ...
 *
 * @PHPCRODM\Document(referenceable=true)
 */
class ContainerBlock extends BaseBlock
{
    /** @PHPCRODM\Children */
    protected  $children;

    public function getType()
    {
        return 'symfony_cmf.block.container';
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren(ChildrenCollection $children)
    {
        return $this->children = $children;
    }
}
