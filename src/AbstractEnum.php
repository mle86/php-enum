<?php

namespace mle86\Enum;

use mle86\Enum\Exception\EnumValueException;
use mle86\Value\AbstractSerializableValue;
use mle86\Value\InvalidArgumentException;
use function in_array;

/**
 * This base class implements part of the {@see Enum} interface:
 * its {@see isValid} test method simply checks
 * if the input is contained in the {@see all()} list.
 * It also implements a constructor with useful error messages
 * in case of invalid input.
 *
 * Additionally, it provides the {@see validate()} and {@see validateOrNull()} helper methods.
 */
abstract class AbstractEnum extends AbstractSerializableValue implements Enum
{

    public function __construct($value)
    {
        try {
            parent::__construct($value);
        } catch (InvalidArgumentException $e) {
            throw EnumValueException::forClass($value, static::class);
        }
    }

    /**
     * This default implementation considers all values in the {@see all()} list valid (as it should be).
     * It also considers all existing instances of the same enum class valid,
     * as they've already passed the same check once.
     *
     * @param mixed|static $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        return ($value instanceof static || in_array($value, static::all(), true));
    }

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
     * but always accepts NULL values
     * (even if they {@see isValid} test method does not accept them).
     *
     * This is useful for optional values.
     *
     * @param mixed|static|null $value The value to test.
     * @param string|null $forKey See {@see validate()}.
     * @return void Returns if the value is valid or NULL.
     * @throws EnumValueException if the input value is not NULL and not valid.
     */
    public static function validateOrNull($value, string $forKey = null): void
    {
        if ($value === null) {
            // ok, we explicitly allow this here without further checks
            return;
        }

        // value is non-null, so it has to pass the regular validate() checks:
        static::validate($value, $forKey);
    }

}
