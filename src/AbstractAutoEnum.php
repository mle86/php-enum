<?php

namespace mle86\Enum;

/**
 * This base class contains a default {@see all()} implementation
 * that always returns the values of all public class constants.
 *
 * This makes writing usable enum classes very easy:
 * extend this class, put some constants in it, done.
 */
abstract class AbstractAutoEnum extends AbstractEnum
{

    private static $_all_list = [];

    /**
     * Returns a list of all public constant values in this class.
     *
     * @return array
     */
    public static function all(): array
    {
        $className = static::class;
        if (!isset(self::$_all_list[$className])) {
            self::$_all_list[$className] = Misc::getPublicConstants($className);
        }
        return self::$_all_list[$className];
    }

}
