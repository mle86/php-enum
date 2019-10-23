<?php
namespace mle86\Enum;

use mle86\Enum\Exception\EnumValueException;
use mle86\Enum\Tests\Helper\MixedTypeEnum;
use mle86\Enum\Tests\Helper\MyString;
use mle86\Enum\Tests\Helper\RestrictiveEnum;
use PHPUnit\Framework\TestCase;

/**
 * Tests {@see AbstractEnum}'s type safety.
 *
 * It uses the {@see RestrictiveEnum} class
 * which only accepts `(int)100` as its input
 * for input type juggling tests.
 *
 * It uses the {@see MixedTypeEnum} class
 * for return type tests.
 */
class EnumTypeTest extends TestCase
{

    public static function validInputs(): array { return [
        // For RestrictiveEnum
        [100],
    ]; }

    public static function invalidInputs(): array { return [
        // For RestrictiveEnum
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

    public static function mixedTypeInputs(): array {
        // For MixedTypeEnum
        return array_map(function($v){ return [$v]; }, MixedTypeEnum::all());
    }

    /**
     * @dataProvider validInputs
     * @param int $validInput
     */
    public function testValidInput(int $validInput): void
    {
        RestrictiveEnum::validate($validInput);
    }

    /**
     * @dataProvider invalidInputs
     * @depends testValidInput
     * @param $invalidInput
     */
    public function testInvalidInput($invalidInput): void
    {
        $this->expectException(EnumValueException::class);
        RestrictiveEnum::validate($invalidInput);
    }


    /**
     * @dataProvider mixedTypeInputs
     * @param $validInput
     */
    public function testAcceptValidMixedInputTypes($validInput): void
    {
        MixedTypeEnum::validate($validInput);
    }

    /**
     * Thanks to {@see testAcceptValidMixedInputTypes} we already know that very different input types
     * can be accepted as valid.
     * Now we have to make sure that even similar-looking values ("", null, false)
     * are not accidentally mixed up or type-coerced.
     *
     * @depends testAcceptValidMixedInputTypes
     */
    public function testMixedInputTypes(): void
    {
        /** @var MixedTypeEnum[] $previousInstances */
        $previousInstances = [];

        foreach (MixedTypeEnum::all() as $validInput) {
            $instance = new MixedTypeEnum($validInput);

            // make sure its stored value is unique:
            foreach ($previousInstances as $knownInstance) {
                $this->assertNotSame($knownInstance->value(), $instance->value());
            }

            $previousInstances[] = $instance;
        }
    }

    /**
     * @dataProvider mixedTypeInputs
     * @depends testMixedInputTypes
     */
    public function testOutputValueTypes($validInput): void
    {
        $instanceValue = (new MixedTypeEnum($validInput))->value();
        $this->assertSameEnumValue($validInput, $instanceValue);
    }

    /**
     * All {@see AbstractEnum} implementations are descendants of {@see AbstractSerializableValue}
     * so they are {@see \JsonSerializable} as well!
     * Make sure json_decode(json_encode(enumInstance)) still returns the correct type and value.
     *
     * @dataProvider mixedTypeInputs
     * @depends testOutputValueTypes
     * @depends testMixedInputTypes
     */
    public function testJsonOutputValueTypes($validInput): void
    {
        $jsonValue = $this->jsonTranscode(new MixedTypeEnum($validInput));
        $this->assertSameEnumValue($validInput, $jsonValue, true);
    }


    private function assertSameEnumValue($inputValue, $enumOutputValue, bool $allowFloatConversionDelta = false): void
    {
        if (is_float($inputValue) && $allowFloatConversionDelta) {
            // here we allow for a tiny conversion leeway:
            $delta = 1e-7;
            $this->assertEqualsWithDelta($inputValue, $enumOutputValue, $delta, "Float value mismatch");

        } else {
            // Values of all other types must match exactly:
            $this->assertSame($inputValue, $enumOutputValue);
        }
    }

    private function jsonTranscode($inputValue)
    {
        $encoded = json_encode($inputValue);
        if (json_last_error() !== \JSON_ERROR_NONE) {
            throw new \RuntimeException("json_encode failed: " . json_last_error_msg());
        }

        $decoded = json_decode($encoded, true);
        if (json_last_error() !== \JSON_ERROR_NONE) {
            throw new \RuntimeException("json_decode failed: " . json_last_error_msg());
        }

        return $decoded;
    }

}
