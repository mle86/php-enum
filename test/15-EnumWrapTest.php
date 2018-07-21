<?php

namespace mle86\Enum;

use mle86\Enum\Tests\Helper\AssertException;
use mle86\Enum\Tests\Helper\FirstTenPrimesEnum;
use PHPUnit\Framework\TestCase;

/**
 * The {@see AbstractEnum} base class
 * inherits several class methods from {@see AbstractValue}.
 *
 * Those are well-tested themselves,
 * we'll just make sure there are no typing/validity problems with enums.
 */
class EnumWrapTest extends TestCase
{
    use AssertException;

    public function testWrapSingleValue(): void
    {
        $value = FirstTenPrimesEnum::PRIME4;

        $instance = $value;
        FirstTenPrimesEnum::wrap($instance);
        /** @var FirstTenPrimesEnum $instance */
        $this->assertInstanceOf(FirstTenPrimesEnum::class, $instance);
        $this->assertSame($value, $instance->value());

        // double wrap:
        $doubleWrapped = $instance;
        FirstTenPrimesEnum::wrap($doubleWrapped);
        $this->assertInstanceOf(FirstTenPrimesEnum::class, $doubleWrapped);
        $this->assertSame($value, $doubleWrapped->value());

        // failing:
        $this->assertException(\InvalidArgumentException::class, function() {
            $invalidValue = -6;
            FirstTenPrimesEnum::validate($invalidValue);
        });
    }

    /**
     * @depends testWrapSingleValue
     */
    public function testWrapMultipleValues(): void
    {
        $fnTestWrapArray = function(array $input): void {
            $wrapped = $input;
            FirstTenPrimesEnum::wrapArray($wrapped);
            /** @var iterable|FirstTenPrimesEnum[] $wrapped */
            $this->assertContainsOnlyInstancesOf(FirstTenPrimesEnum::class, $wrapped);

            // check equality:
            foreach ($input as $idx => $value) {
                $inputValue   = ($value instanceof Enum) ? $value->value() : $value;
                $wrappedValue = $wrapped[$idx]->value();
                $this->assertSame($inputValue, $wrappedValue);
            }
        };

        $fnTestWrapArray([]);
        $fnTestWrapArray([FirstTenPrimesEnum::PRIME4]);
        $fnTestWrapArray([FirstTenPrimesEnum::PRIME4, new FirstTenPrimesEnum(FirstTenPrimesEnum::PRIME5)]);
    }

    /**
     * @depends testWrapMultipleValues
     */
    public function testWrapOrNullMultipleValues(): void
    {
        $fnTestWrapOrNullArray = function(array $input): void {
            $wrapped = $input;
            FirstTenPrimesEnum::wrapOrNullArray($wrapped);
            /** @var iterable|FirstTenPrimesEnum[]|null[] $wrapped */

            // check output types:
            foreach ($wrapped as $wrappedValue) {
                if ($wrappedValue !== null && !($wrappedValue instanceof FirstTenPrimesEnum)) {
                    throw new \RuntimeException("Enum::wrapOrNullArray() result contained something else than Enum and NULL: " . gettype($wrappedValue));
                }
            }

            // check equality:
            foreach ($input as $idx => $value) {
                $inputValue   = ($value         instanceof Enum) ? $value        ->value() : $value;
                $wrappedValue = ($wrapped[$idx] instanceof Enum) ? $wrapped[$idx]->value() : $wrapped[$idx];
                $this->assertSame($inputValue, $wrappedValue);
            }
        };

        $fnTestWrapOrNullArray([]);
        $fnTestWrapOrNullArray([null]);
        $fnTestWrapOrNullArray([FirstTenPrimesEnum::PRIME4]);
        $fnTestWrapOrNullArray([FirstTenPrimesEnum::PRIME4, null]);
        $fnTestWrapOrNullArray([FirstTenPrimesEnum::PRIME4, new FirstTenPrimesEnum(FirstTenPrimesEnum::PRIME5)]);
        $fnTestWrapOrNullArray([FirstTenPrimesEnum::PRIME4, new FirstTenPrimesEnum(FirstTenPrimesEnum::PRIME5), null]);
    }

}
