<?php

namespace Symfony\Cmf\Bundle\MultilangContentBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * A field that has to be translated
 * @author brian.king (at) liip.ch
 *
 * @Annotation
 * @Target("PROPERTY")
 */
final class Translated extends Annotation
{
    public $type = 'translated';
}

/**
 * The field that specifies the current language of the document
 * @author brian.king (at) liip.ch
 *
 * @Annotation
 * @Target("PROPERTY")
 */
final class Language extends Annotation
{
    public $type = 'language';
}

/**
 * Class annotation for multilanguage documents
 *
 * Can have a strategy attribute to specify the multilang storage strategy.
 * Defaults to child nodes if not specified.
 *
 * @author david.buchmann (at) liip.ch
 *
 * @Annotation
 * @Target("CLASS")
 */
final class Multilang extends Annotation
{
    public $type = 'multilang';
    public $strategy;
}
