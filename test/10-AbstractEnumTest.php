<?php

namespace mle86\Enum;

use mle86\Enum\AbstractEnum;
use mle86\Enum\Tests\Helper\AssertException;
use mle86\Enum\Tests\Helper\FirstTenPrimesEnum;
use mle86\Enum\Exception\EnumValueException;
use PHPUnit\Framework\TestCase;

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
        [array(7)],
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
            $this->assertException(EnumValueException::class, function () use ($invalid_value) {
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

}
