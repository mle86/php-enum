<?php

namespace mle86\Enum\Tests\Helper;

use mle86\Enum\AbstractAutoEnum;

/**
 * @internal
 */
final class TestAutoEnum extends AbstractAutoEnum
{

    public const C10 = 10;
    public const C20 = 20;
    const C30 = 30;  // no 'public'

    protected const W40 = 40;

    private const Y50 = 50;

}
