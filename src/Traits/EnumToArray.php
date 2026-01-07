<?php

namespace Istoy\Traits;

/**
 * Make Enums interact as Array
 */
trait EnumToArray
{
    /**
     * Return Enum Names
     *
     * @return array
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }
    
    /**
     * Return Enum Values
     *
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Return Enum as array
     *
     * @return array
     */
    public static function array(): array
    {
        return array_combine(self::names(), self::values());
    }
}

