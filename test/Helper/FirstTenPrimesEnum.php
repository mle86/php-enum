<?php

namespace mle86\Enum\Tests\Helper;

use mle86\Enum\AbstractEnum;

class FirstTenPrimesEnum extends AbstractEnum
{

    public const PRIME1  = 2;
    public const PRIME2  = 3;
    public const PRIME3  = 5;
    public const PRIME4  = 7;
    public const PRIME5  = 11;
    public const PRIME6  = 13;
    public const PRIME7  = 17;
    public const PRIME8  = 19;
    public const PRIME9  = 23;
    public const PRIME10 = 29;

    public static function all(): array
    {
        return [2, 3, 5, 7, 11, 13, 17, 19, 23, 29];
    }

}
