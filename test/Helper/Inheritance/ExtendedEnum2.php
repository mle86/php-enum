<?php

namespace mle86\Enum\Tests\Helper\Inheritance;

/**
 * @internal
 */
final class ExtendedEnum2 extends BaseEnum
{

    public const Y1 = 1003;
    public const Y2 = 1004;

    public static function all(): array { return [
        self::Y1,
        self::Y2,
    ]; }

}
