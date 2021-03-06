<?php

namespace mle86\Enum;

use mle86\Enum\Exception\EnumValueException;
use mle86\Enum\Tests\Helper\AssertException;
use mle86\Enum\Tests\Helper\TestAutoEnum;
use mle86\Enum\Tests\Helper\TestEmptyAutoEnum;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality of the library's {@see AbstractAutoEnum} base class.
 *
 * This test class is based on two included dummy implementation, {@see TestAutoEnum} and {@see TestEmptyAutoEnum},
 * which are extensions of the {@see AbstractAutoEnumTest} base class.
 *
 * The following tests are performed:
 *
 *  - Ensure that all public class constants are recognized as valid enum values.
 *  - Ensure that protected and private class constants are _not_ recognized as valud enum values.
 *  - Ensure that the `all()` class method returns all public constants' values and nothing else.
 *  - Ensure that an empty auto-enum class still works as intended, that is:
 *     - Its `all()` method returns an empty array.
 *     - Several test values accepted by a different auto-enum class are rejected.
 */
class AbstractAutoEnumTest extends TestCase
{
    use AssertException;

    public static function validValues(): array { return [
        // all valid for TestAutoEnum:
        [TestAutoEnum::C10],  // public const C10 = 10
        [TestAutoEnum::C20],  // public const C20 = 20
        [TestAutoEnum::C30],  // const C30 = 30
        // This list must be _complete_ or testAllValidValues() will fail.
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
        TestAutoEnum::validateOptional($valid_value);

        $instance = new TestAutoEnum($valid_value);
        $this->assertSame($valid_value, $instance->value());
        $this->assertTrue(TestAutoEnum::isValid($instance));
        TestAutoEnum::validateOptional($instance);
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
            // we cannot test that here, validateOptional would _accept_ that value
        } else {
            $this->assertException(EnumValueException::class, function () use ($invalid_value) {
                TestAutoEnum::validateOptional($invalid_value);
            });
        }

        $this->assertException(EnumValueException::class, function() use($invalid_value) {
            return new TestAutoEnum($invalid_value);
        });
    }

    /**
     * Ensure that {@see AbstractAutoEnum::all()} returns everything.
     *
     * @depends testValidValues
     */
    public function testAllValidValues(): void
    {
        $fn_unwrap = function(array $input) { return reset($input); };
        $knownList = array_map($fn_unwrap, self::validValues());

        $classList = TestAutoEnum::all();

        // The AbstractAutoEnum base class makes no promises about `all()`'s ordering,
        // so we need to sort both lists once:
        sort($knownList);
        sort($classList);

        $this->assertSame($knownList, $classList);
    }

    /**
     * Ensure that {@see AbstractAutoEnum::all()} returns an associative array
     * with the enum const names as keys.
     *
     * @depends testAllValidValues
     */
    public function testAllValidKeys(): void
    {
        $classList = TestAutoEnum::all();

        $this->assertArrayHasKey('C10', $classList);
        $this->assertArrayHasKey('C20', $classList);
        $this->assertArrayHasKey('C30', $classList);
        $this->assertArrayNotHasKey('W40', $classList);
        $this->assertArrayNotHasKey('Y50', $classList);
        $this->assertCount(3, $classList);

        $this->assertSame(TestAutoEnum::C10, $classList['C10']);
        $this->assertSame(TestAutoEnum::C30, $classList['C30']);
    }

    /**
     * @depends testAllValidValues
     */
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
