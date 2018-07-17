<?php
namespace mle86\Enum;

use mle86\Enum\Exception\EnumValueException;
use mle86\Enum\Tests\Helper\Inheritance\BaseEnum;
use mle86\Enum\Tests\Helper\Inheritance\ExtendedEnum1;
use mle86\Enum\Tests\Helper\Inheritance\ExtendedEnum2;
use mle86\Enum\Tests\Helper\MyString;
use mle86\Enum\Tests\Helper\RestrictiveEnum;
use PHPUnit\Framework\TestCase;

/**
 * Tests {@see AbstractEnum}'s type safety.
 *
 * It uses the {@see RestrictiveEnum} class
 * which only accepts `(int)100` as its input
 * for input type juggling tests.
 */
class EnumTypeTest extends TestCase
{

    public static function validInputs(): array { return [
        [100],
    ]; }

    public static function invalidInputs(): array { return [
        // Multiple inputs that looks sort of right but are of an invalid type:
        ["100"],
        ["100."],
        ["+100"],
        [array("100")],
        [array(100)],
        [(object)100],
        [new MyString(100)],
        [100.001],
        [-100],
        ["-100"],
    ]; }

    /**
     * @dataProvider validInputs
     * @param int $validInput
     */
    public function testValidInstance(int $validInput): void
    {
        RestrictiveEnum::validate($validInput);
    }

    /**
     * @dataProvider invalidInputs
     * @depends testValidInstance
     * @param $invalidInput
     */
    public function testInvalidInput($invalidInput): void
    {
        $this->expectException(EnumValueException::class);
        RestrictiveEnum::validate($invalidInput);
    }

}
