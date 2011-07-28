<?php

namespace Symfony\Cmf\Bundle\CoreBundle\Helper;

#TODO: remove this class and use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser
#      when and if https://github.com/symfony/symfony/pull/1386 gets merged
class ExtensionGuesser extends \Symfony\Component\HttpFoundation\File\File
{
    static function guess($mimeType)
    {
        return isset(static::$defaultExtensions[$mimeType]) ? static::$defaultExtensions[$mimeType] : null;
    }

}
