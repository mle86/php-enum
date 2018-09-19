<?php

namespace mle86\Enum\Exception;

use mle86\Enum\Enum;
use mle86\Value\Value;

/**
 * Thrown in case of an invalid invalid value.
 *
 * These exceptions are thrown by the {@see Enum::__construct} constructor (implemented in {@see AutoEnum})
 * and by the {@see EnumValidationTrait::validate}… methods (used in {@see AutoEnum}).
 */
class EnumValueException extends \InvalidArgumentException implements EnumException
{

    /*
     * Don't use the default constructor --
     * the forClass/forKey constructors add useful extra information to the exception
     * which can later be retrieved with the getInvalidValue/getUsedKey getters.
     */

    private $invalidValue;
    private $usedKey;

    public static function forClass($value, $enumClass): self
    {
        $shortClass = self::getShortClassName($enumClass);
        $shortValue = self::getShortValue($value);

        $o = new self("not a valid {$shortClass}: {$shortValue}");
        $o->invalidValue = $value;
        return $o;
    }

    public static function forKey($value, string $key): self
    {
        $shortValue = self::getShortValue($value);

        $o = new self("not a valid '{$key}': {$shortValue}");
        $o->invalidValue = $value;
        $o->usedKey      = $key;
        return $o;
    }

    /**
     * Returns the invalid value that was the reason for this exception.
     *
     * For this to work, it must have been instantiated through the {@see forClass} or {@see forKey} constructors
     * (the {@see EnumValidationTrait::validate}… methods do that correctly).
     *
     * @return mixed
     */
    public function getInvalidValue()
    {
        return $this->invalidValue;
    }

    /**
     * Returns the input key that was used with an invalid value.
     *
     * For this to work, it must have been instantiated through the {@see forKey} constructor
     * (the {@see EnumValidationTrait::validate}… methods do that correctly if a key is provided).
     *
     * @return string|null
     */
    public function getUsedKey(): ?string
    {
        return $this->usedKey;
    }

    private static function getShortClassName($class): string
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $p = strrpos($class, '\\');
        if ($p !== false) {
            $class = substr($class, $p + 1);
        }

        return $class;
    }

    private static function getShortValue($value, int $stringLimit = 50): string
    {
        if ($value === null) {
            return 'null';
        }

        if (is_string($value)) {
            $length = mb_strlen($value);
            if ($length > $stringLimit) {
                $value = mb_substr($value, 0, $stringLimit) . '…';
            }
            $safeValue = addslashes($value);
            return "'{$safeValue}'";
        }

        if (is_int($value) || is_float($value)) {
            return (string)$value;
        }

        if (is_bool($value)) {
            return ($value ? 'true' : 'false');
        }

        if (is_array($value)) {
            return 'array(' . count($value) . ')';
        }

        if (is_resource($value)) {
            $type = get_resource_type($value);
            if ($type === null || $type === false || $type === 'Unknown') {
                // fallback
                return 'resource';
            }

            return 'resource (' . $type . ')';
        }

        if ($value instanceof Enum || $value instanceof Value) {
            return '(' . self::getShortClassName($value) . ')' . self::getShortValue($value->value());
        }

        if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                return '(' . get_class($value) . ')' . self::getShortValue((string)$value, $stringLimit);
            }
            if ($value instanceof \Countable) {
                return get_class($value) . '(' . count($value) . ')';
            }
            // just some object, we can't show anything else if we don't want to serialize it:
            return get_class($value);
        }

        // ?!
        return gettype($value);
    }

}
