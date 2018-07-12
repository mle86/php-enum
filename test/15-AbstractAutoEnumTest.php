<?php

namespace mle86\Enum;

use mle86\Enum\Exception\EnumValueException;
use mle86\Enum\Tests\Helper\AssertException;
use mle86\Enum\Tests\Helper\TestAutoEnum;
use mle86\Enum\Tests\Helper\TestEmptyAutoEnum;
use PHPUnit\Framework\TestCase;

class AbstractAutoEnumTest extends TestCase
{
    use AssertException;

    public static function validValues(): array { return [
        // all valid for TestAutoEnum:
        [TestAutoEnum::C10],  // public const C10 = 10
        [TestAutoEnum::C20],  // public const C20 = 20
        [TestAutoEnum::C30],  // const C30 = 30
    ]; }

    public static function invalidValues(): array { return [
        // not valid for TestAutoEnum:
        [40],  // protected const W40
        [50],  // private const Y50
        [60],
        [- TestAutoEnum::C10],
        [TestAutoEnum::C10 * 1.1],
        [(string)TestAutoEnum::C10],
        [null],
    ]; }


    /**
     * @dataProvider validValues
     * @param int $valid_value
     */
    public function testValidValues(int $valid_value): void
    {
        $this->assertTrue(TestAutoEnum::isValid($valid_value));

        TestAutoEnum::validate($valid_value);
        TestAutoEnum::validateOrNull($valid_value);

        $instance = new TestAutoEnum($valid_value);
        $this->assertSame($valid_value, $instance->value());
        $this->assertTrue(TestAutoEnum::isValid($instance));
        TestAutoEnum::validateOrNull($instance);
    }

    /**
     * @dataProvider invalidValues
     * @param $invalid_value
     */
    public function testInvalidValues($invalid_value): void
    {
        $this->assertFalse(TestAutoEnum::isValid($invalid_value));

        $this->assertException(EnumValueException::class, function() use($invalid_value) {
            TestAutoEnum::validate($invalid_value);
        });

        if ($invalid_value === null) {
            // we cannot test that here, validateOrNull would _accept_ that value
        } else {
            $this->assertException(EnumValueException::class, function () use ($invalid_value) {
                TestAutoEnum::validateOrNull($invalid_value);
            });
        }

        $this->assertException(EnumValueException::class, function() use($invalid_value) {
            return new TestAutoEnum($invalid_value);
        });
    }

    public function testEmptyEnumClass(): void
    {
        $this->assertEmpty(TestEmptyAutoEnum::all());
    }

    /**
     * @dataProvider validValues
     * @dataProvider invalidValues
     * @depends testInvalidValues
     * @depends testEmptyEnumClass
     * @param mixed $value
     */
    public function testEmptyEnumValues($value): void
    {
        $this->assertFalse(TestEmptyAutoEnum::isValid($value));
    }

}
