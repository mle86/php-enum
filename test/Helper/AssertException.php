<?php

namespace mle86\Enum\Tests\Helper;

use mle86\Enum\Exception\EnumValueException;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;

trait AssertException
{

    /**
     * Executes the callback (without any arguments)
     * and expects it to throw an exception whose type is `$exception_class` or a subclass of that.
     *
     * @param string|string[] $exception_class  The expected exception FQCN.
     *                                          Can be an array of FQCNs if multiple exception classes are allowed.
     * @param callable $callback  The callback to invoke.
     * @param string $message  The assertion error message.
     * @return \Throwable  Returns the caught exception.
     */
    protected function assertException($exception_class, callable $callback, string $message = ''): \Throwable
    {
        $ex = null;

        try {
            $callback();
        } catch (\Throwable $ex) {
            // continue
        }

        $joined_fqcn = implode('|', (array)$exception_class);
        $message = "Callback should have thrown a {$joined_fqcn}!" .
            (($message !== '') ? "\n" . $message : '');

        /** @var TestCase $this */
        $this->assertNotNull($ex, $message);

        foreach ((array)$exception_class as $fqcn) {
            if (is_a($ex, $fqcn)) {
                // ok!
                return $ex;
            }
        }

        throw new AssertionFailedError($message, 0, $ex);
    }

    protected function assertExceptionMessageContainsWord(string $word, \Exception $e, string $message = ''): void
    {
        if ($message === '') {
            $message = 'Enum exception message does not contain expected word!';
        }

        $regex = '/(?:\b|^|(?<=\s))' . preg_quote($word, '/') . '(?:\b|$|(?=\s))/u';
        $this->assertRegExp($regex, $e->getMessage(), $message);
    }

    protected function assertExceptionMessageContainsValue($value, \Exception $e): void
    {
        $this->assertExceptionMessageContainsWord((string)$value, $e,
            'Enum exception message does not contain the invalid input value!');
    }

    protected function assertExceptionContainsValueCopy($value, EnumValueException $e): void
    {
        $this->assertSame($value, $e->getInvalidValue(),
            'Enum exception does not contain the original invalid value!');
    }

}
