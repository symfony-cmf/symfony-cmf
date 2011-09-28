<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Knp\Menu\NodeInterface;

/**
 * This class represents a menu item for the cmf.
 *
 * @PHPCRODM\Document(alias="menu_item")
 */
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

    /** @PHPCRODM\String */
    protected $route;

    protected $weakContent;

    protected $strongContent;

    /** @PHPCRODM\Boolean */
    protected $weak = true;

    /** @PHPCRODM\String(multivalue=true) */
    protected $attributes;

    /** @PHPCRODM\Children(filter="*item") */
    protected $children;


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

    public function getRoute()
    {
        return $this->route;
    }

    public function setRoute($route)
    {
        $this->route = $route;
    }

    public function getContent()
    {
        if ($this->weak) {
            return $this->weakContent;
        } else {
            return $this->stringContent;
        }
    }

    public function setContent($content)
    {
        if ($this->weak) {
            $this->weakContent = $content;
        } else {
            $this->strongContent = $content;
        }
    }

    public function getWeak()
    {
        return $this->weak;
    }

    public function setWeak($weak)
    {
        if ($this->weak && !$weak) {
            $this->strongContent = $this->weakContent;
            $this->weakContent = null;
        } elseif (!$this->weak && $weak) {
            $this->weakContent = $this->strongContent;
            $this->strongContent = null;
        }
        $this->weak = $weak;
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

    public function getOptions()
    {
        return array(
            'uri' => $this->getUri(),
            'route' => $this->getRoute(),
            'label' => $this->getLabel(),
            'attributes' => $this->getAttributes(),
            'display' => true,
            'displayChildren' => true,
            // TODO
            'routeParameters' => array(),
            'routeAbsolute' => false,
            'linkAttributes' => array(),
            'labelAttributes' => array(),
        );
    }
}
