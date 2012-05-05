<?php

namespace Symfony\Cmf\Bundle\MultilangContentBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Cmf\Component\Routing\RouteAwareInterface;
use Symfony\Cmf\Bundle\ContentBundle\Document\StaticContent;

/**
 * @PHPCRODM\Document(translator="child")
 */
class MultilangStaticContent extends StaticContent
{
    /**
     * @PHPCRODM\Locale
     */
    public $locale;

    /**
     * @Assert\NotBlank
     * @PHPCRODM\String(translated=true)
     */
    public $name;

    /**
     * @Assert\NotBlank
     * @PHPCRODM\String(translated=true)
     */
    public $title;

    /**
     * @PHPCRODM\String(translated=true)
     */
    public $body;

}
