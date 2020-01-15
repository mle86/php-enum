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

        $output = [];
        foreach ($reflectionClass->getReflectionConstants() as $rc) {
            if (!$rc->isPublic()) {
                continue;
            }

            $output[$rc->getName()] = $rc->getValue();
        }

        return $output;
    }

}
