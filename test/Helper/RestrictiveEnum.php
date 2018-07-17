<?php

namespace mle86\Enum\Tests\Helper;

use mle86\Enum\AbstractEnum;

/**
 * @internal
 *   This dummy implementation is used in the {@see EnumExceptionTest} class.
 *   It only accepts one input value: 100.
 */
final class RestrictiveEnum extends AbstractEnum
{

    public const ONLY_ALLOWED_VALUE = 100;

    public static function all(): array { return [
        self::ONLY_ALLOWED_VALUE,
    ]; }

}
