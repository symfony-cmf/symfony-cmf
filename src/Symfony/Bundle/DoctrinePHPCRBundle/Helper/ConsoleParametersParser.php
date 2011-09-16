<?php

namespace Symfony\Bundle\DoctrinePHPCRBundle\Helper;

/**
 * @author Daniel Barsotti <daniel.barsotti@liip.ch>
 */
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
