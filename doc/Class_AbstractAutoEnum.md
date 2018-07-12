# `AbstractAutoEnum` Base Class

This base class contains a default `all()` implementation
that always returns the values of all public class constants.

This makes writing usable enum classes very easy:
extend this class, put some constants in it, done.

[Exceptions]: Exceptions.md
[Enum]: Class_Enum.php
[AbstractEnum]: Class_AbstractEnum.php
[AbstractAutoEnum]: Class_AbstractAutoEnum.php


## Class Details

* Declaration: <code>class mle86\\Enum\\<b>AbstractAutoEnum</b> extends mle86\\Enum\\[AbstractEnum]</code>.
* Class file: [src/AbstractAutoEnum.php](../src/AbstractAutoEnum.php)


## Method Reference

* <code>static <b>all</b> (): array</code>  
    Returns a list of all public constant values in this class.


## Inherited Methods

* **Constructor:** `__construct ($value)`
* <code>static <b>isValid</b> ($value): bool</code>
* <code>static <b>validate</b> ($value, string $forKey = null): void</code>
* <code>static <b>validateOptional</b> ($value, string $forKey = null): void</code>
* <code><b>value</b> (): mixed</code>
* <code><b>jsonSerialize</b> (): mixed</code>
* <code><b>__toString</b> (): string</code>
* <code><b>equals</b> ($value): bool</code>

All inherited methods are documented
in the [AbstractEnum] class documentation.
