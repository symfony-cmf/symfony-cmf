<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/**
 * Block that contains other blocks ...
 *
 * @PHPCRODM\Document
 */
class ContainerBlock extends BaseBlock
{
    /** @PHPCRODM\String */
    private $title;

    /** @PHPCRODM\Children */
    private $children;

    public function getType()
    {
        return 'symfony_cmf.block.container';
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getChildren()
    {
        return $this->children->getValues();
    }
}
