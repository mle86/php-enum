<?php

namespace mle86\Enum\Tests\Helper\Inheritance;

/**
 * @internal
 */
final class ExtendedEnum1 extends BaseEnum
{

    public const X1 = 1004;
    public const X2 = 1005;

    public static function all(): array { return [
        self::X1,
        self::X2,
    ]; }

}
