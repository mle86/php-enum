# php-enum

[![Build Status](https://travis-ci.org/mle86/php-enum.svg?branch=master)](https://travis-ci.org/mle86/php-enum)
[![Coverage Status](https://coveralls.io/repos/github/mle86/php-enum/badge.svg?branch=master)](https://coveralls.io/github/mle86/php-enum?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mle86/enum/version)](https://packagist.org/packages/mle86/enum)
[![License](https://poser.pugx.org/mle86/enum/license)](https://packagist.org/packages/mle86/enum)


This PHP library
provides `enum` functionality for PHP 7.1+
with an [Enum] interface,
an [AbstractEnum] base class,
and an [AbstractAutoEnum] base class.

It is released under the [MIT License](https://opensource.org/licenses/MIT).


## Installation and Dependencies:

Via Composer:  `$ composer require mle86/enum`

Or insert this into your project's `composer.json` file:

```json
"require": {
    "mle86/enum": "^0"
}
```

Its only dependency is the
[mle86/value](https://github.com/mle86/php-value) library.


## Minimum PHP Version:

PHP 7.1


## Usage:

To **implement a custom Enum class,**
simply extend the [AbstractAutoEnum] base class
and add a few public constants:

```php
<?php
class TriState extends \mle86\Enum\AbstractAutoEnum
{
    public const HIGH  = 'H';
    public const LOW   = 'L';
    public const UNDEF = 'Z';
}
```

For more fine control
over which values are considered valid by your class,
override the `isValid()` class method or
extend the more general [AbstractEnum] base class instead.  
Keep in mind though that enum classes
are fundamentally based on
a *hardcoded list of accepted values* known at compile-time;
if you want to accept values based on a pattern or on complex validation logic,
consider using a value object class
such as [Value](https://github.com/mle86/php-value)
instead.


There's three ways to
**use enum classes:**

1. Just use the class constants.  
    You don't really need this library to do that
    as you can simply write a class without methods for that,
    but it's definitely a possibility.

2. Build instances and use typehints in your methods.  
    The [AbstractEnum] and [AbstractAutoEnum] base classes
    have a default constructor
    and wraps a single valid enum value
    in the instance.
    The wrapped value can then be retrieved
    with the `value()` getter
    but it's also returned by the
    default `__toString()` and `jsonSerialize()` methods.

3. Use the `validate()` and `validateOptional()` methods
    to enfore correct types in your methods.  
    If you don't want to build instances of your enum classes
    to avoid the object overhead
    or simply because you receive non-object input values
    (e.g. from a JSON API),
    you can also check raw input values
    with the two `validate` methods.
    They will accept both raw values and instances,
    return void,
    and throw an [EnumValueException][Exceptions]
    if the input is invalid.
    The `validateOptional()` method also accepts NULL values.


An example with instances and enum typehints:

```php
<?php
$state = new TriState(TriState::HIGH);

var_dump($state->value());     // H
var_dump(json_encode($state)); // "H"
var_dump($state->equals(TriState::HIGH)); // true
var_dump($state->equals(TriState::LOW));  // false

function (TriState $state) {
    // $state is definitely a TriState instance here,
    // so it definitely wraps a valid TriState constant.
}
```


Another example without typehints
and more explicit validation:

```php
<?php
function ($state, $optionalState = null) {
    TriState::validate($state);
    // Now we can be sure that $state contains a valid TriState value
    // (or maybe it's even a TriState instance).
    
    TriState::validateOptional($optionalState);
    // Now we know that $optionalState contains either
    // a valid TriState value,
    // a valid TriState instance,
    // or NULL.
}
```


## Classes and Interfaces:

* [Enum] base interface:
  * [AbstractEnum] base class.
    * [AbstractAutoEnum] base class.
* [AutoEnumTrait] trait.
* [Exception][Exceptions] classes.

[Enum]: doc/Class_Enum.md
[AbstractAutoEnum]: doc/Class_AbstractAutoEnum.md
[AbstractEnum]: doc/Class_AbstractEnum.md
[AutoEnumTrait]: doc/Class_AutoEnumTrait.md
[Exceptions]: doc/Exceptions.md
