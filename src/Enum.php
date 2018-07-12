<?php

namespace mle86\Enum;

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
     * Wraps one value in a {@see Value} instance.
     * The constructor will ensure that the value is actually allowed by this enum class.
     *
     * @param mixed $value
     */
    public function __construct($value);

    /**
     * @return mixed
     *   Returns the value wrapped by this enum instance.
     */
    public function value();

    /**
     * @return iterable
     *   Returns a list of all valid values in this enum class.
     */
    public static function all(): iterable;

    /**
     * Tests if one value is considered valid by this enum class.
     *
     * @param mixed $value
     * @return bool
     */
    public static function isValid($value): bool;

}
