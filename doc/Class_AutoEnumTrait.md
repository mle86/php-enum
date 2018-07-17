# `AutoEnumTrait` Trait

This trait can be added to any Enum class
that wants a [AbstractAutoEnum]-style
default implementation of the `all()` class method.

(In fact the [AbstractAutoEnum] class uses this trait as well.)

This might be useful in case of [enum class hierarchies](Enum_Inheritance.md)
where you don't want your abstract base classes to have an `all()` method,
but you'll still need it in the leaf subclasses.

[AbstractEnum]: Class_AbstractEnum.md
[AbstractAutoEnum]: Class_AbstractAutoEnum.md


## Class Details

* Declaration: <code>trait mle86\\Enum\\<b>AutoEnumTrait</b></code>
* Class file: [src/AutoEnumTrait.php](../src/AutoEnumTrait.php)


## Method Reference

* <code>static <b>all</b> (): array</code>  
    Returns a list of all public constant values in this class.
