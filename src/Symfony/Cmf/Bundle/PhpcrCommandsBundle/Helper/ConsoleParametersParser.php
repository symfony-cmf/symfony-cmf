<?php

/*
 * This file is part of the Symfony/Cmf/PhpcrCommandsBundle
 *
 * (c) Daniel Barsotti <daniel.barsotti@liip.ch>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\Cmf\Bundle\PhpcrCommandsBundle\Helper;

class ConsoleParametersParser
{
    /**
     * Return true if $value is a string that can be considered as true.
     * I.e. if it is case insensitively "true" or "yes".
     * @param string $value
     * @return boolean
     */
    public static function isTrueString($value)
    {
        $value = strtolower($value);
        return $value === 'true' || $value === 'yes';
    }

    /**
     * Return true if $value is a string that can be considered as false.
     * I.e. if it is case insensitively "false" or "no".
     * @param string $value
     * @return boolean
     */
    public static function isFalseString($value)
    {
        $value = strtolower($value);
        return $value === 'false' || $value === 'no';
    }
}