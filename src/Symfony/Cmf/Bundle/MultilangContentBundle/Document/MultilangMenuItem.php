<?php

namespace Symfony\Cmf\Bundle\MultilangContentBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Knp\Menu\NodeInterface;

/**
 * This class represents a multilanguage menu item for the cmf.
 *
 * The label and uri are translatable, to have a language specific menu caption
 * and to be able to have external links language specific.
 *
 * To protect against accidentally injecting things into the tree, all menu
 * item node names must end on -item.
 *
 * @PHPCRODM\Document(translator="attribute")
 */
class MultilangMenuItem implements NodeInterface {

    /**
     * to create the document at the specified location. read only for existing documents.
     *
     * @PHPCRODM\Id
     */
    protected $path;

    /** @PHPCRODM\Locale */
    public $locale;

    /** @PHPCRODM\String */
    protected $name;

    /** @PHPCRODM\String(translated=true) */
    protected $label;

    /** @PHPCRODM\Uri(translated=true) */
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
        return array_combine($this->attributeKeys, $this->attributes);
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        $this->attributeKeys = array_keys($attributes);
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
            'content' => $this->getContent(),
            // TODO provide the following information
            'routeParameters' => array(),
            'routeAbsolute' => false,
            'linkAttributes' => array(),
            'labelAttributes' => array(),
        );
    }
}
