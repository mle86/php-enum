<?php

namespace mle86\Enum\Tests\Helper;

use mle86\Enum\AbstractEnum;

/**
 * @internal
 *   This dummy enum accepts a few different input values
 *   and they're all different PHP types!
 *   (Except for "97" and "", just to see if ""/`null`/`false` are treated differently)
 */
class MixedTypeEnum extends AbstractEnum
{

    public const ZSV     = "";
    public const STRINGV = "97";
    public const INTV    = 98;
    public const FLOATV  = 99.9;
    public const NULLV   = null;  // Yes, that's also a valid enum value here
    public const BOOLV   = false;

    public static function all(): array { return [
        self::ZSV,
        self::STRINGV,
        self::INTV,
        self::FLOATV,
        self::NULLV,
        self::BOOLV,
    ]; }

}
