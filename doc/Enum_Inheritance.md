# Enum Inheritance

Enum inheritance is tricky.

Normal class inheritance
means that subclasses can add or alter functionality
while keeping or overriding existing functionality.

How this should map to enum classes is unclear
and many languages that support enums in the first place
don't support enum inheritance at all.

**This library attempts to support basic enum inheritance**
by making <code>[AbstractEnum]::isValid()</code>
accept all subclass instances.
This has a few notable effects:

- Enum class hierarchies work as expected:
  all `MyExtendedEnum` instances are also `MyBaseEnum` instances,
  but not vice versa.

- Despite this,
  subclass instances cannot be assumed to be re-wrappable by the parent class –
  `new MyBaseEnum($extendedEnum)` might fail.

- `MyBaseEnum::isValid()` will now accept `MyExtendedEnum` (subclass) instances,
  but `MyBaseEnum::__construct()` probably won't
  as base classes are under no obligation
  to keep track of their subclasses (and their constants).

- In the same vein,
  `MyBaseEnum::all()` will not return any subclass constants.

- The `MyBaseEnum::validate()` helper method
  won't work as expected on raw values,
  but will return successfully
  both on `MyBaseEnum` and `MyExtendedEnum` instances.

To limit the problems
stemming from these sometimes counter-intuitive effects,
here are several recommendations for enum inheritance.

[AbstractEnum]: Class_AbstractEnum.md
[AbstractAutoEnum]: Class_AbstractAutoEnum.md
[AutoEnumTrait]: Class_AutoEnumTrait.md


## Recommendations for Enum Inheritance with this Library

1. Avoid if possible.

2. Extend only abstract enum classes
  that do not contain any public constants,
  and do not write an `all()` implementation for those base classes.

3. If possible,
  only extend Enums derived from [AbstractEnum]
  but not from [AbstractAutoEnum]
  as the latter contains an `all()` implementation
  that won't work correctly on custom abstract enum base classes.  
  (You can use the [AutoEnumTrait] in the subclasses
  to get the same `all()` implementation.)

4. Non-abstract enum classes should be `final`
  to prevent subclasses from adding new constants,
  overriding `all()`/`isValid()`,
  and changing existing constants.


Reasoning:

- Having only abstract base enum classes
  ensures that you can never re-wrap an existing subclass instance
  in a parent class instance
  (which would lead to weird instances without self-validity).

- Not implementing `all()` in base enum classes
  makes it obvious that these classes
  have no knowledge about their subclasses
  and are therefore not capable
  of validating raw input values
  by themselves.

- Making non-abstract enum classes `final`
  guarantees that no subclass instances
  with completely unknown values
  will pop up.


## Examples

### A valid hierarchy example:

```php
<?php

abstract class CarPartType extends AbstractEnum { }
abstract class EngineType extends CarPartType { }

class CombustionEngineType extends EngineType
{
    use AutoEnumTrait;

    const DIESEL = 1;
    const PETROL = 2;
    const LNG    = 3;
}

class ElectricEngineType extends EngineType
{
    use AutoEnumTrait;

    const AC_MOTOR = 90;
    const DC_MOTOR = 91;
}
```

Positive effects:

- Your methods can now typehint for generic `EngineType $engine` arguments, accepting any engine type instance.
- Your methods can now typehint for more specific `CombustionEngineType $engine` arguments, accepting only instances of that class.
- `EngineType::isValid()` will accept _all subclass instances._

Undesirable effects:

- `EngineType::isValid()` will accept no subclass _values,_ as it knows about none of them.
- `EngineType::isValid()` will fail with an ugly “Cannot call abstract method all()” error on any other input value.
- `EngineType::validate()` is likewise nonfunctional on raw values.
- There are no automatic checks in place to ensure that the various `EngineType` subclass values do not accidentally overlap.
  (While there are no checks like that in our [AbstractEnum] base class either,
  having all constants in a single class at least makes mistakes like that easier to spot.)

