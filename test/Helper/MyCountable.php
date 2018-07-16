<?php

namespace mle86\Enum\Tests\Helper;


/**
 * @internal
 */
class MyCountable implements \Countable
{

    private $n;
    public function __construct(int $n)
    {
        $this->n = $n;
    }

    public function count(): int
    {
        return $this->n;
    }

}
