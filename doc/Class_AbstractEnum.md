# `AbstractEnum` Base Class

This base class implements part of the [Enum] interface:

its `isValid` test method simply checks
if the input is contained in the `all()` list.
It also implements a constructor with useful error messages
in case of invalid input.

Additionally, it provides the `validate()` and `validateOptional()` helper methods.

[Exceptions]: Exceptions.md
[Enum]: Class_Enum.md
[AbstractEnum]: Class_AbstractEnum.md
[AbstractAutoEnum]: Class_AbstractAutoEnum.md
[AbstractSerializableValue]: ../vendor/mle86/value/src/Value/AbstractSerializableValue.php
[AbstractValue]: ../vendor/mle86/value/src/Value/AbstractValue.php


## Class Details

* Declaration: <code>class mle86\\Enum\\<b>AbstractEnum</b> extends mle86\\Value\\[AbstractSerializableValue] implements mle86\\Enum\\[Enum]</code>
* Class file: [src/AbstractEnum.php](../src/AbstractEnum.php)


## Method Reference

* **Constructor:** `__construct ($value)`  
    Default implementation
    according to the [Enum] interface contract.

* <code>static <b>isValid</b> ($value): bool</code>  
    This default implementation considers all values in the `all()` list valid (as it should be).
    It also considers all existing instances of the same enum class valid,
    as they've already passed the same check once.

* <code>static <b>validate</b> ($value, string $forKey = null): void</code>  
    This helper method validates an existing value
    without constructing an enum instance.  
    Throws an [EnumValueException][Exceptions]
    if the input value is not valid.

* <code>static <b>validateArray</b> iterable $values, string $forKey = null): void</code>  
    Like `validate()`,
    but ensures that the input is an array (or other `iterable`)
    and that _all_ values in it pass the `validate()` check
    (i.e. are an enum instance or an enum value).  
    Empty input arrays are acceptable.

* <code>static <b>validateOptional</b> ($value, string $forKey = null): void</code>  
    Like `validate()`,
    but always accepts NULL values
    (even if the `isValid` test method does not accept them).  
    This is useful for optional values.  
    Throws an [EnumValueException][Exceptions]
    if the input value is not NULL and not valid.

* <code>static <b>validateOptionals</b> ($value, string $forKey = null): void</code>  
    Like `validateOptional()`,
    but ensures that the input is an array (or other `iterable`)
    and that _all_ values in it pass the `validateOptional()` check
    (i.e. are an enum instance, an enum value, or `NULL`).


## Inherited Methods

* <code>abstract static <b>all</b> (): iterable</code>  
    Returns a list of all valid values in this enum class.  
    Inherited from the [Enum] interface.

* <code><b>value</b> (): mixed</code>  
  <code><b>jsonSerialize</b> (): mixed</code>  
  <code><b>__toString</b> (): string</code>  
    Returns the value wrapped by this enum instance.  
    Inherited from the [AbstractValue] and [AbstractSerializableValue] base classes.

* <code><b>equals</b> ($value): bool</code>  
    This method performs an equality check on other instances or raw values.
    Objects are considered equal if and only if they are instances of the same
    class and carry the same `value()`.  All other values are considered equal
    if and only if they are identical (===) to the current objects's `value()`.  
    Inherited from the [AbstractValue] base class.
