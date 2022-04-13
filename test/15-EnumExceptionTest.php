<?php
namespace mle86\Enum;

use mle86\Enum\Exception\EnumValueException;
use mle86\Enum\Tests\Helper\AssertException;
use mle86\Enum\Tests\Helper\FirstTenPrimesEnum;
use mle86\Enum\Tests\Helper\MyCountable;
use mle86\Enum\Tests\Helper\MyString;
use mle86\Enum\Tests\Helper\RestrictiveEnum;
use PHPUnit\Framework\TestCase;

/**
 * Tests {@see EnumValueException}'s value output.
 *
 * This exception class tried hard to show a short, human-readable, safe representation of invalid input values
 * to simplify debugging.
 * Some of these tests may already be done in {@see AbstractEnumTest}
 * but now we're testing everything.
 */
class EnumExceptionTest extends TestCase
{
    use AssertException;


    public static function numberRepresentations(): array { return [
        // ints:
        [0,     "0"],
        [10,    "10"],
        [1000,  "1000"],
        [-1,    "-1"],
        [-10,   "-10"],
        [-1000, "-1000"],
        // big ints:
        [99999999999,  "99999999999"],
        [-99999999999, "-99999999999"],
        // floats:
        [0.0,  "0"],
        [0.2,  "0.2"],
        [-8.8, "-8.8"],
    ]; }

    public static function booleanRepresentations(): array { return [
        [true,  "true"],
        [false, "false"],
    ]; }

    public static function otherRepresentations(): array { return [
        [null,            "null"],
        [new \stdClass,   "stdClass"],
        [array(),         "array"],
        [array(11,22,33), "array"],
    ]; }

    public static function resourceRepresentations(): array { return [
        [fopen('php://stdin', 'rb'), "stream"],
    ]; }

    public static function countableRepresentations(): array { return [
        [array(),            "array(0)"],
        [array(66,77,88,99), "array(4)"],
        [new MyCountable(0), "MyCountable(0)"],
        [new MyCountable(7), "MyCountable(7)"],
    ]; }


    /**
     * Ensures that the exception message contains the enum's class name.
     */
    public function testMessageBase(): void
    {
        $this->withInvalidInput(
            5031,
            function(EnumValueException $e): void {
                $fqcn = RestrictiveEnum::class;
                $shcn = 'RestrictiveEnum';

                $reFqcnOrShcn = '(?:' . preg_quote($fqcn, '/') . '|' . preg_quote($shcn, '/') . ')';

                $this->assertMatchesRegularExpression("/^(?:not a valid|invalid) {$reFqcnOrShcn}\b/u", $e->getMessage());
            });
    }

    /**
     * Some input types are easy to output.
     *
     * @dataProvider numberRepresentations
     * @dataProvider booleanRepresentations
     * @dataProvider otherRepresentations
     * @depends testMessageBase
     * @param $invalidInput
     * @param string $expectedRepresentation
     */
    public function testDefaultRepresentations($invalidInput, string $expectedRepresentation): void
    {
        $this->withInvalidInput(
            $invalidInput,
            function(EnumValueException $e) use($expectedRepresentation, $invalidInput): void {
                $this->assertExceptionMessageContainsWord($expectedRepresentation, $e);

                // The exception class also contains a special getter to get the original invalid value:
                $this->assertSame($invalidInput, $e->getInvalidValue());
            });
    }

    /**
     * @dataProvider resourceRepresentations
     * @depends testDefaultRepresentations
     * @param $invalidInput
     * @param string $expectedRepresentation
     */
    public function testResourceRepresentations($invalidInput, string $expectedRepresentation): void
    {
        $this->withInvalidInput(
            $invalidInput,
            function(EnumValueException $e) use($expectedRepresentation): void {
                $this->assertExceptionMessageContainsWord("resource", $e);
                if ($expectedRepresentation !== '') {
                    $this->assertExceptionMessageContainsWord($expectedRepresentation, $e);
                }
            });
    }

    /**
     * @depends testDefaultRepresentations
     */
    public function testStringRepresentations(): void
    {
        $fnAssert = function($invalidInput, string $regex = null) {
            if ($regex === null) {
                // By default just make sure the entire invalid input it contained in the exception msg:
                $regex = "/'" . preg_quote((string)$invalidInput, '/') . "'/u";
            }

            $this->withInvalidInput(
                $invalidInput,
                function(EnumValueException $e) use($regex): void {
                    $this->assertMatchesRegularExpression($regex, $e->getMessage());
                });
        };

        // By default just make sure the entire invalid input it contained in the exception msg:
        $fnAssert("");
        $fnAssert("Hello!");

        // Long strings should be shortened:
        $fnAssert(
            str_repeat("ABCDEFGHIJKLMNO ", 1000),
            '/\'ABCDEFGHIJKLMNO.*\'/u');

        // Shortening should be done with mb_substr
        // i.e. it should never corrupt UTF-8 characters:
        $fnAssert(
            str_repeat("Ä", 1000),
            '/\'.*ÄÄÄÄÄ(?:\.\.\.|…| ?<\w+>)?\'/u');

        $fnAssert(new MyString(""));
        $fnAssert(new MyString("Hello!"));
        $fnAssert(
            new MyString(str_repeat("ABCDEFGHIJKLMNO ", 1000)),
            '/\'ABCDEFGHIJKLMNO.*\'/u');
    }

    /**
     * @dataProvider countableRepresentations
     * @depends testDefaultRepresentations
     */
    public function testCountableRepresentations($countableInput, string $expectedRepresentation): void
    {
        $this->withInvalidInput(
            $countableInput,
            function(EnumValueException $e) use($expectedRepresentation): void {
                $this->assertExceptionMessageContainsWord($expectedRepresentation, $e);
            });
    }

    /**
     * @depends testDefaultRepresentations
     */
    public function testOtherEnumClass(): void
    {
        $class = 'FirstTenPrimesEnum';
        $value = FirstTenPrimesEnum::PRIME5;
        $other = new FirstTenPrimesEnum($value);

        $this->withInvalidInput(
            $other,
            function(EnumValueException $e) use($class, $value) {
                $regex = "/\b{$class}.+{$value}\b/u";
                $this->assertMatchesRegularExpression($regex, $e->getMessage());
            });
    }

    /**
     * @depends testDefaultRepresentations
     */
    public function testSpecialGetters(): void
    {
        $fnTestGetters = function($invalidValue, ?string $useKey): void {
            /** @var EnumValueException $ex */
            $ex = $this->assertException(EnumValueException::class, function() use($invalidValue, $useKey) {
                RestrictiveEnum::validate($invalidValue, $useKey);
            });

            // getInvalidValue() should return the original invalid value unchanged:
            $this->assertSame($invalidValue, $ex->getInvalidValue());
            // getUsedKey() should return the original validate($forKey) string argument unchanged:
            $this->assertSame($useKey, $ex->getUsedKey());
        };

        $fnTestGetters("foo*bar", null);
        $fnTestGetters(-91.33,    null);

        $fnTestGetters("zog+baz", 'k1');
        $fnTestGetters(-97.22,    'myInputKey');
    }


    private function withInvalidInput($invalidInput, callable $callback): void
    {
        /** @var EnumValueException $exception */
        $exception = $this->assertException(EnumValueException::class, function() use($invalidInput) {
            return new RestrictiveEnum($invalidInput);
        });

        $this->assertExceptionContainsValueCopy($invalidInput, $exception);

        $callback($exception);
    }

}
