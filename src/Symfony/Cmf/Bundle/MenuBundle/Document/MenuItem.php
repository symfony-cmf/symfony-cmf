<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Knp\Menu\NodeInterface;
use Doctrine\Common\Collections\Collection;

/**
 * This class represents a menu item for the cmf.
 *
 * To protect against accidentally injecting things into the tree, all menu
 * item node names must end on -item.
 *
 * @PHPCRODM\Document
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

    /** @PHPCRODM\Uri */
    protected $uri;

    /** @PHPCRODM\String */
    protected $route;

    /** @PHPCRODM\ReferenceOne */
    protected $weakContent;

    /** @PHPCRODM\ReferenceOne(weak=false) */
    protected $strongContent;

    /** @PHPCRODM\Boolean */
    protected $weak = true;

    /**
     * Simulate a php hashmap in phpcr. This holds the keys
     *
     * @PHPCRODM\String(multivalue=true)
     */
    protected $attributeKeys;

    /**
     * Simulate a php hashmap in phpcr. This holds the keys
     *
     * @PHPCRODM\String(multivalue=true)
     */
    protected $attributes;

    /**
     * Simulate a php hashmap in phpcr. This holds the keys.
     *
     * @PHPCRODM\String(multivalue=true)
     */
    protected $childrenAttributeKeys;

    /**
     * Simulate a php hashmap in phpcr.
     *
     * @PHPCRODM\String(multivalue=true)
     */
    protected $childrenAttributes;

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
        }
        return $this->strongContent;
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
        if (is_null($this->attributeKeys)) {
            return array();
        }
        $keys = $this->attributeKeys instanceof Collection ?
            $this->attributeKeys->toArray() :
            $this->attributeKeys;
        $values = $this->attributes instanceof Collection ?
            $this->attributes->toArray() :
            $this->attributes;
        return array_combine($keys, $values);
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        $this->attributeKeys = array_keys($attributes);
    }

    public function getChildrenAttributes()
    {
        if (is_null($this->childrenAttributeKeys)) {
            return array();
        }
        $keys = $this->childrenAttributeKeys instanceof Collection ?
            $this->childrenAttributeKeys->toArray() :
            $this->childrenAttributeKeys;
        $values = $this->childrenAttributes instanceof Collection ?
            $this->childrenAttributes->toArray() :
            $this->childrenAttributes;
        return array_combine($keys, $values);
    }

    public function setChildrenAttributes($attributes)
    {
        $this->childrenAttributes = $attributes;
        $this->childrenAttributeKeys = array_keys($attributes);
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
            'childrenAttributes' => $this->getChildrenAttributes(),            
            'display' => true,
            'displayChildren' => true,
            'content' => $this->getContent(),
            // TODO provide the following information
            'routeParameters' => array(),
            'routeAbsolute' => false,
            'linkAttributes' => array(),
            'labelAttributes' => array(),
        );
    }
}
