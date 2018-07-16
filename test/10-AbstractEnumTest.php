<?php

namespace mle86\Enum;

use mle86\Enum\AbstractEnum;
use mle86\Enum\Tests\Helper\AssertException;
use mle86\Enum\Tests\Helper\FirstTenPrimesEnum;
use mle86\Enum\Exception\EnumValueException;
use mle86\Value\AbstractSerializableValue;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality of the library's {@see AbstractEnum} base class.
 *
 * This test class is based on an included dummy implementation, {@see FirstTenPrimesEnum},
 * which is an extension of the {@see AbstractEnum} base class.
 *
 * The following tests are performed:
 *
 *  - Ensure that known valid values are accepted,
 *    both by the class constructor, the `isValid()` test method, and the `validate()` assertion method.
 *  - Ensure that known invalid and known illegal values are rejected,
 *    both by the class constructor, the `isValid()` test method, and the `validate()` assertion method.
 *    This includes otherwise valid input values cast to a different type (e.g. `"7"` vs `7`).
 *  - Ensure that the `validateOptional()` assertion method actually accepts `null` values.
 *  - Ensure that enum instances are considered valid themselves
 *    and can be fed to the constructor to build an identical instance.
 *  - Ensure that the serialization of instances (string typecasting and `json_encode`)
 *    return the enum constant as expected.
 *  - Ensure that the equality operator (`==`)
 *    and the equality test method (`equals()`)
 *    work as expected,
 *    both on other instances, instances of other classes, and on raw values.
 *  - Ensures that {@see EnumValueException}s
 *    throws by the constructor and the `validate()` assertion method
 *    reflect the offending value in their error message.
 */
class AbstractEnumTest extends TestCase
{
    use AssertException;

    public static function validInput(): array { return [
        [7],
        [11],
        [29],
    ]; }

    public static function invalidInput(): array { return [
        [8],
        [0],
        [-3],
    ]; }

    public static function illegalInput(): array { return [
        [null],  // considered invalid, but validateOptional() won't complain about it
        [false],
        [3.3],
        [new \stdClass],
        ['7'],  // valid number, but invalid type (string instead of int)
        [array(7)],
    ]; }

    public static function readableInvalidInput(): array { return [
        // Used in testException().
        [4444],
        ['11'],
        ['foo*bar'],
        [new class(11) extends AbstractEnum { public static function all(): array { return [ 11 ]; } }],
        [new class { public function __toString(): string { return '11'; } }],
    ]; }


    /**
     * @dataProvider validInput
     * @param int $valid_value
     */
    public function testValidValues(int $valid_value): void
    {
        $this->assertTrue(FirstTenPrimesEnum::isValid($valid_value));
        FirstTenPrimesEnum::validate($valid_value);
        FirstTenPrimesEnum::validateOptional($valid_value);
    }

    /**
     * @dataProvider validInput
     * @depends testValidValues
     * @param int $valid_value
     */
    public function testInstantiateValidValues(int $valid_value): void
    {
        $instance = new FirstTenPrimesEnum($valid_value);
        $this->assertSame($valid_value, $instance->value());
        $this->assertTrue(FirstTenPrimesEnum::isValid($instance));
        $this->assertTrue($instance->equals($valid_value));
        $this->assertTrue($instance->equals($instance));
    }

    /**
     * @dataProvider invalidInput
     * @dataProvider illegalInput
     * @depends testValidValues
     * @depends testInstantiateValidValues
     * @param $invalid_value
     */
    public function testInvalidValues($invalid_value): void
    {
        $this->assertFalse(FirstTenPrimesEnum::isValid($invalid_value));

        $this->assertException(EnumValueException::class, function() use($invalid_value) {
            FirstTenPrimesEnum::validate($invalid_value);
        });

        if ($invalid_value === null) {
            // we cannot test that here, validateOptional would _accept_ that value
        } else {
            $this->assertException(EnumValueException::class, function() use ($invalid_value) {
                FirstTenPrimesEnum::validateOptional($invalid_value);
            });
        }

        $this->assertException(EnumValueException::class, function() use($invalid_value) {
            return new FirstTenPrimesEnum($invalid_value);
        });
    }

    /**
     * @depends testValidValues
     * @depends testInvalidValues
     */
    public function testValidateNull(): void
    {
        FirstTenPrimesEnum::validateOptional(null);
    }

    /**
     * @depends testInstantiateValidValues
     */
    public function testRewrapInstance(): void
    {
        $valid_value = FirstTenPrimesEnum::PRIME8;

        $instance = new FirstTenPrimesEnum($valid_value);
        $instance = new FirstTenPrimesEnum($instance);
        $instance = new FirstTenPrimesEnum($instance);

        $this->assertTrue(FirstTenPrimesEnum::isValid($instance));
        $this->assertSame($valid_value, $instance->value());
    }

    /**
     * @depends testInstantiateValidValues
     */
    public function testSerialization(): void
    {
        $valid_value = FirstTenPrimesEnum::PRIME9;

        $instance = new FirstTenPrimesEnum($valid_value);

        $this->assertSame((string)$valid_value, (string)$instance);
        $this->assertTrue(($instance == (string)$valid_value));

        $this->assertSame(json_encode($valid_value), json_encode($instance));
        $this->assertEquals($instance, unserialize(serialize($instance)));
    }

    /**
     * @depends testValidValues
     * @depends testInvalidValues
     * @depends testSerialization
     */
    public function testEquality(): void
    {
        $valid_value = FirstTenPrimesEnum::PRIME7;
        $other_value = FirstTenPrimesEnum::PRIME3;

        $instance = new FirstTenPrimesEnum($valid_value);
        $this->assertSame($valid_value, $instance->value());
        $this->assertTrue(((string)$valid_value == (string)$instance));
        $this->assertTrue($instance->equals($valid_value));

        // different instance, same value inside:
        $similar_instance = new FirstTenPrimesEnum($valid_value);
        $this->assertTrue(($instance == $similar_instance));
        $this->assertTrue(($similar_instance == $instance));
        $this->assertTrue($similar_instance->equals($valid_value));
        $this->assertTrue($similar_instance->equals($instance));
        $this->assertTrue($instance->equals($similar_instance));

        // different instance with different value inside:
        $other_instance = new FirstTenPrimesEnum($other_value);
        $this->assertFalse(($instance == $other_instance));
        $this->assertFalse(($other_instance == $instance));
        $this->assertFalse($other_instance->equals($valid_value));
        $this->assertFalse($other_instance->equals($instance));
        $this->assertFalse($instance->equals($other_instance));

        // This other enum class accepts some of the exact same values!
        // Still, it's a different class.
        $foreign_instance = new class($valid_value) extends AbstractEnum {
            public static function all(): iterable
            {
                return [FirstTenPrimesEnum::PRIME3, FirstTenPrimesEnum::PRIME7];
            }
        };
        $this->assertFalse(($instance == $foreign_instance));
        $this->assertFalse(($foreign_instance == $instance));
        $this->assertTrue($foreign_instance->equals($valid_value));  // it's the same raw value
        $this->assertFalse($foreign_instance->equals($instance));  // different class
        $this->assertFalse($instance->equals($foreign_instance));  // different class
    }

    /**
     * @dataProvider readableInvalidInput
     * @depends testInstantiateValidValues
     * @depends testInvalidValues
     */
    public function testException($invalid_value): void
    {
        $expected_value = (string)$invalid_value;
        if ($invalid_value === null) {
            // we expect to see "null" in the exception msg now
            $expected_value = 'null';
        }

        /** @var EnumValueException $ex */
        $ex = $this->assertException(EnumValueException::class,
            function() use($invalid_value) { return new FirstTenPrimesEnum($invalid_value); });
        $this->assertExceptionContainsValueCopy   ($invalid_value, $ex);
        $this->assertExceptionMessageContainsValue($expected_value, $ex);
        $this->assertExceptionMessageContainsWord ('FirstTenPrimesEnum', $ex);

        /** @var EnumValueException $ex */
        $ex = $this->assertException(EnumValueException::class,
            function() use($invalid_value) { FirstTenPrimesEnum::validate($invalid_value, 'my_prime'); });
        $this->assertExceptionContainsValueCopy   ($invalid_value, $ex);
        $this->assertExceptionMessageContainsValue($expected_value, $ex);
        $this->assertExceptionMessageContainsWord ('my_prime', $ex);
    }


    private function assertExceptionMessageContainsWord(string $word, \Exception $e, string $message = ''): void
    {
        if ($message === '') {
            $message = 'Enum exception message does not contain expected word!';
        }

        $regex = '/\b' . preg_quote($word, '/') . '\b/u';
        $this->assertRegExp($regex, $e->getMessage(), $message);
    }

    private function assertExceptionMessageContainsValue($value, \Exception $e): void
    {
        $this->assertExceptionMessageContainsWord((string)$value, $e,
            'Enum exception message does not contain the invalid input value!');
    }

    private function assertExceptionContainsValueCopy($value, EnumValueException $e): void
    {
        $this->assertSame($value, $e->getInvalidValue(),
            'Enum exception does not contain the original invalid value!');
    }

}
