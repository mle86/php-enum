<?php

namespace mle86\Enum;


/**
 * This trait can be added to any Enum class
 * that wants a {@see AbstractAutoEnum}-style
 * default implementation of the {@see all()} class method.
 *
 * (In fact the {@see AbstractAutoEnum} class uses this trait as well.)
 *
 * This might be useful in case of enum class hierarchies
 * where you don't want your abstract base classes to have an {@see all()} method,
 * but you'll still need it in the leaf subclasses.
 */
trait AutoEnumTrait
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
