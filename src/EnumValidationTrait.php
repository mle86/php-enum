<?php

namespace mle86\Enum;

use mle86\Enum\Exception\EnumValueException;

/**
 * This trait contains the enum validation helper methods
 * ({@see validate}, {@see validateOptional},
 *  {@see validateArray}, {@see validateOptionals}).
 *
 * It is used by the {@see AbstractEnum} base class
 * (and therefore by the {@see AbstractAutoEnum} base class as well).
 */
trait EnumValidationTrait
{

    /**
     * This helper method validates an existing value
     * without constructing an enum instance.
     *
     * @param mixed|static $value The value to test.
     * @param string|null $forKey If the value is invalid, the method will throw an {@see EnumValueException} with a default “not a valid <i>class</i>” message.
     *                            If this parameter is set, the exception message will read “not a valid '<i>key</i>'” instead.
     * @return void Returns if the value is valid.
     * @throws EnumValueException if the input value is not valid.
     */
    public static function validate($value, string $forKey = null): void
    {
        if (!static::isValid($value)) {
            // invalid!
            if ($forKey !== null) {
                throw EnumValueException::forKey($value, $forKey);
            } else {
                throw EnumValueException::forClass($value, static::class);
            }
        }
    }

    /**
     * Like {@see validate()},
     * but ensures that the input is an array (or other `iterable`)
     * and that _all_ values in it pass the {@see validate()} check
     * (i.e. are an enum instance or an enum value).
     *
     * Empty input arrays are acceptable.
     *
     * @param iterable|mixed[]|static[] $values
     * @param string|null $forKey
     * @return void if all input values are valid enum values or instances.
     * @throws EnumValueException if at least one of the input values is not valid.
     */
    public static function validateArray(iterable $values, string $forKey = null): void
    {
        foreach ($values as $value) {
            self::validate($value, $forKey);
        }
    }

    /**
     * Like {@see validate()},
     * but always accepts NULL values
     * (even if the {@see isValid} test method does not accept them).
     *
     * This is useful for optional values.
     *
     * @param mixed|static|null $value The value to test.
     * @param string|null $forKey See {@see validate()}.
     * @return void Returns if the value is valid or NULL.
     * @throws EnumValueException if the input value is not NULL and not valid.
     */
    public static function validateOptional($value, string $forKey = null): void
    {
        if ($value === null) {
            // ok, we explicitly allow this here without further checks
            return;
        }

        // value is non-null, so it has to pass the regular validate() checks:
        static::validate($value, $forKey);
    }

    /**
     * Like {@see validateOptional()},
     * but ensures that the input is an array (or other `iterable`)
     * and that _all_ values in it pass the {@see validateOptional()} check
     * (i.e. are an enum instance, an enum value, or `NULL`).
     *
     * @param iterable|mixed[]|static[]|null[] $values
     * @param string|null $forKey
     * @return void Returns if the input array contains only enum values, enum instances, and/or NULLs.
     * @throws EnumValueException if the input array contains at least one non-NULL value that is not valid.
     */
    public static function validateOptionals(iterable $values, string $forKey = null): void
    {
        foreach ($values as $value) {
            self::validateOptional($value, $forKey);
        }
    }

}
