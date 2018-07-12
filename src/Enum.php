<?php

namespace mle86\Enum;

use mle86\Enum\Exception\EnumValueException;
use mle86\Value\Value;

/**
 * Common interface for all Enum classes.
 *
 * Interface contract:
 *  - All enum classes have an {@see all()} class method that returns all valid values.
 *  - All enum classes have an {@see isValid()} class method that tests single values.
 *  - All enum classes can be instantiated with a valid value which they will then wrap.
 *  - All enum classes have a {@see value()} method which returns the wrapped value.
 */
interface Enum extends Value
{

    /**
     * Wraps one value in an {@see Enum} instance.
     * The value can later be accessed with the {@see value()} getter.
     *
     * The constructor will ensure that the value is actually allowed by this enum class
     * using the {@see isValid()} test method.
     *
     * It is possible to use other instances of the same class as input.
     * In this case, their wrapped value will be re-wrapped,
     * resulting in two identical instances.
     *
     * @param mixed $value
     * @throws EnumValueException if the input value is not valid.
     */
    public function __construct($value);

    /**
     * Returns the value wrapped by this enum instance.
     *
     * @return mixed
     */
    public function value();

    /**
     * Returns a list of all valid values in this enum class.
     *
     * Everything returned by this method is a valid input for the constructor
     * and will pass the {@see isValid()} test method.
     *
     * The list should only contain unique values.
     *
     * @return iterable
     */
    public static function all(): iterable;

    /**
     * Tests if a value is considered valid by this enum class.
     *
     * Instances of the same class are also considered valid.
     *
     * @param mixed|static $value
     * @return bool
     */
    public static function isValid($value): bool;

}
