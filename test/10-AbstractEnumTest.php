<?php

namespace mle86\Enum;

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
        [null],  // considered invalid, but validateOrNull() won't complain about it
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
        FirstTenPrimesEnum::validateOrNull($valid_value);
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
            // we cannot test that here, validateOrNull would _accept_ that value
        } else {
            $this->assertException(EnumValueException::class, function () use ($invalid_value) {
                FirstTenPrimesEnum::validateOrNull($invalid_value);
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
        FirstTenPrimesEnum::validateOrNull(null);
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

}
