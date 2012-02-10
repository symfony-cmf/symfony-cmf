<?php

namespace Symfony\Cmf\Bundle\MultilangContentBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;

use Doctrine\Common\Collections\Collection;
use Knp\Menu\NodeInterface;
use Symfony\Cmf\Bundle\MenuBundle\Document\MenuItem;

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
class MultilangMenuItem extends MenuItem {

    /** @PHPCRODM\Locale */
    protected $locale;

    /** @PHPCRODM\String(translated=true) */
    protected $label;

    /** @PHPCRODM\Uri(translated=true) */
    protected $uri;

}
