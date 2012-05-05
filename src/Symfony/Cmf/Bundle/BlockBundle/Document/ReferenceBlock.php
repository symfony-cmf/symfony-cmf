<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/**
 * Block that is a reference to another block
 *
 * @PHPCRODM\Document(referenceable=true)
 */
class ReferenceBlock extends BaseBlock
{

    /** @PHPCRODM\ReferenceOne */
    private $referencedBlock;

    public function getType()
    {
        return 'symfony_cmf.block.reference';
    }

    public function getReferencedBlock()
    {
        return $this->referencedBlock;
    }

    public function setReferencedBlock($referencedBlock)
    {
        return $this->referencedBlock = $referencedBlock;
    }
}
