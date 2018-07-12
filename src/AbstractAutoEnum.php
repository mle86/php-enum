<?php

namespace mle86\Enum;

/**
 * This base class contains a default {@see all()} implementation
 * that always returns all public class constants.
 *
 * This makes writing usable enum classes very easy:
 * extend this class, put some constants in it, done.
 */
abstract class AbstractAutoEnum extends AbstractEnum
{

    private static $_all_list = [];

    public static function all(): array
    {
        $className = static::class;
        if (!isset(self::$_all_list[$className])) {
            self::$_all_list[$className] = self::getPublicConstants($className);
        }
        return self::$_all_list[$className];
    }

    private static function getPublicConstants(string $className): array
    {
        $reflectionClass = new \ReflectionClass($className);
        $fnPublicFilter = function(\ReflectionClassConstant $constant): bool { return $constant->isPublic(); };
        $fnGetValue = function(\ReflectionClassConstant $constant) { return $constant->getValue(); };

        return array_map($fnGetValue, array_filter(
            $reflectionClass->getReflectionConstants(),
            $fnPublicFilter));
    }

}
