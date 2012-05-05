<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/**
 * Block that contains other blocks ...
 *
 * @PHPCRODM\Document(referenceable=true)
 */
class SimpleBlock extends BaseBlock
{
    /** @PHPCRODM\String */
    private $title;

    /** @PHPCRODM\String */
    private $content;

    public function getType()
    {
        return 'symfony_cmf.block.simple';
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setContent($content)
    {
        $this->content;
    }

    public function getContent()
    {
        return $this->content;
    }
}
