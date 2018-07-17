<?php

namespace mle86\Enum;

/**
 * This helper class contains static methods
 * that are used elsewhere in this library.
 *
 * @internal
 *   There's probably no good reason to use this directly in your project.
 *   The methods may change at any time, even on minor updates.
 */
final class Misc
{

    public static function getPublicConstants(string $className): array
    {
        $reflectionClass = new \ReflectionClass($className);
        $fnPublicFilter = function(\ReflectionClassConstant $constant): bool { return $constant->isPublic(); };
        $fnGetValue = function(\ReflectionClassConstant $constant) { return $constant->getValue(); };

        return array_map($fnGetValue, array_filter(
            $reflectionClass->getReflectionConstants(),
            $fnPublicFilter));
    }

}
