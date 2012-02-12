<?php

namespace Symfony\Cmf\Bundle\BlockBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Sonata\BlockBundle\Model\BlockInterface;


/**
 * Base class for all blocks - connects to Sonata Blocks
 *
 * @PHPCRODM\Document
 */
abstract class BaseBlock implements BlockInterface
{
    /** @PHPCRODM\Id(strategy="parent") */
    protected $id;

    /** @PHPCRODM\Nodename */
    protected $name;

    /** @PHPCRODM\ParentDocument */
    protected $parent;

    /** @PHPCRODM\Boolean */
    protected $enabled = true;

    /** @PHPCRODM\Int */
    protected $position = 1;

    /** @PHPCRODM\Int */
    protected $ttl = 86400;


    /**
     * Set id
     *
     * @param string $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return string $id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     */
    public function setType($type) { }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }

    /**
     * Get enabled
     *
     * @return boolean $enabled
     */
    public function getEnabled() {
        return $this->enabled;
    }

    /**
     * Set position
     *
     * @param integer $position
     */
    public function setPosition($position) {
        $this->position = $position;
    }

    /**
     * Get position
     *
     * @return integer $position
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     * Set createdAt
     *
     * @param \Datetime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt = null) {
        // TODO
    }

    /**
     * Get createdAt
     *
     * @return \Datetime $createdAt
     */
    public function getCreatedAt() {
        // TODO
        return null;
    }

    /**
     * Set updatedAt
     *
     * @param \Datetime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt = null) {
        // TODO
    }

    /**
     * Get updatedAt
     *
     * @return \Datetime $updatedAt
     */
    public function getUpdatedAt() {
        // TODO
        return null;
    }

    /**
     * Add children
     *
     * @param BlockInterface $children
     */
    public function addChildren(BlockInterface $children) { }

    /**
     * Get children
     *
     * @return Doctrine\Common\Collections\Collection $children
     */
    public function getChildren() {
        return null;
    }

    /**
     * @abstract
     * @return bool
     */
    public function hasChildren() {
        return false;
    }

    public function setName($name) {
        $this->name = $name;
    }

    /**
     * set parent document regardless of type
     *
     * @param $document
     */
    public function setParentDocument($parent) {
        $this->parent = $parent;
    }

    /**
     * get the parent document
     *
     * @return $document
     */
    public function getParentDocument() {
        return $this->parent;
    }

    /**
     * Set parent
     *
     * @param BlockInterface $parent
     */
    public function setParent(BlockInterface $parent) {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return BlockInterface $parent
     */
    public function getParent() {
        if ($this->parent instanceof BlockInterface) {
            return $this->parent;
        }
        else {
            return null;
        }
    }

    /**
     * Has this block a parent
     *
     * @return void
     */
    public function hasParent() {
        return ($this->parent instanceof BlockInterface);
    }

    /**
     * Set ttl
     *
     * @param integer $ttl
     */
    public function setTtl($ttl) {
        $this->ttl = $ttl;
    }

    /**
     * Get ttl
     *
     * @return integer
     */
    public function getTtl() {
        return $this->ttl;
    }

    /**
     * toString ...
     *
     * @return string
     */
    public function __toString() {
        return $this->getType() . ' - ' . $this->id;
    }
}
