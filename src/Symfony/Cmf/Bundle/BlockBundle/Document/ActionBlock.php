<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

/**
 * Block that renders an action
 *
 * @PHPCRODM\Document(referenceable=true)
 */
class ActionBlock extends BaseBlock
{

    /** @PHPCRODM\String */
    protected $actionName;

    public function getType()
    {
        return 'symfony_cmf.block.action';
    }

    public function getActionName()
    {
        return $this->actionName;
    }

    public function setActionName($actionName)
    {
        return $this->actionName = $actionName;
    }
}
