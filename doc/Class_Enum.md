# `Enum` Interface

Common interface for all enum classes.

[Exceptions]: Exceptions.md
[Enum]: Class_Enum.php
[AbstractEnum]: Class_AbstractEnum.php
[AbstractAutoEnum]: Class_AbstractAutoEnum.php


## Interface Details

* Declaration: <code>interface mle86\\Enum\\<b>Enum</b></code>
* Interface file: [src/Enum.php](../src/Enum.php)

Interface contract:

 - All enum classes have an `all()` class method that returns all valid values.
 - All enum classes have an `isValid()` class method that tests single values.
 - All enum classes can be instantiated with a valid value which they will then wrap.
 - All enum classes have a `value()` method which returns the wrapped value.


## Method Reference

* **Constructor:** `__construct ($value)`  
    Wraps one value in an `Enum` instance.
    The value can later be accessed with the `value()` getter.
    The constructor will ensure that the value is actually allowed by this enum class
    using the `isValid()` test method.
    It is possible to use other instances of the same class as input.
    In this case, their wrapped value will be re-wrapped,
    resulting in two identical instances.
    Throws an [EnumValueException][Exceptions] if the input value is not valid.

* <code><b>value</b> (): mixed</code>  
    Returns the value wrapped by this enum instance.

* <code>static <b>all</b> (): iterable</code>  
    Returns a list of all valid values in this enum class.
    Everything returned by this method is a valid input for the constructor
    and will pass the `isValid()` test method.
    The list should only contain unique values.

* <code>static <b>isValid</b> ($value): bool</code>  
    Tests if a value is considered valid by this enum class.
    Instances of the same class are also considered valid.
