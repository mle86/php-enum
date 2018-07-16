<?php

namespace mle86\Enum\Tests\Helper;


/**
 * @internal
 */
class MyString
{

    private $s;
    public function __construct(string $s)
    {
        $this->s = $s;
    }

    public function __toString(): string
    {
        return $this->s;
    }

}
