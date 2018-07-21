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
 * Additionally, it provides the {@see validate()} and {@see validateOptional()} helper methods.
 */
abstract class AbstractEnum extends AbstractSerializableValue implements Enum
{
    use EnumValidationTrait;

    /**
     * Default implementation
     * according to the {@see Enum::__construct()} interface contract.
     *
     * @param mixed|static $value
     */
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

}
