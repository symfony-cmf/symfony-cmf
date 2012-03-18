<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/**
 * Block that contains other blocks ...
 *
 * @PHPCRODM\Document(referenceable=true)
 */
class ContainerBlock extends BaseBlock
{
    /** @PHPCRODM\Children */
    private $children;

    public function getType()
    {
        return 'symfony_cmf.block.container';
    }

    public function getChildren()
    {
        return $this->children->getValues();
    }
}
