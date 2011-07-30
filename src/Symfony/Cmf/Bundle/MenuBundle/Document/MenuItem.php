<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Knp\Bundle\MenuBundle\NodeInterface;

/** @PHPCRODM\Document(alias="menu_item") */
class MenuItem implements NodeInterface {

    /**
     * to create the document at the specified location. read only for existing documents.
     *
     * @PHPCRODM\Id
     */
    protected $path;

    /** @PHPCRODM\String */
    protected $name;

    /** @PHPCRODM\String */
    protected $label;

    /** @PHPCRODM\String */
    protected $uri;

    /** @PHPCRODM\String(multivalue=true) */
    protected $attributes;

    /** @PHPCRODM\Children(filter="*item") */
    protected $children;

    public function initialize(array $options = array())
    {
    }


    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    public function getAttributes()
    {
        return $this->attributes === null ? array() : $this->attributes;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function getChildren()
    {
        return $this->children;
    }

}
