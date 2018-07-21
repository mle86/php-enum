# Enum Type Safety

Enum classes are built around their constants.

PHP supports class constants
with various types
including arrays.
For Enum usage it is recommended
to stick to one type per Enum class
(for example, do not mix string and int constants in one class)
and to choose a scalar type for them
(`string`, `int`, `bool`, or `null`).
Float constants are possible too
but might be problematic
due to hardware-specific rounding
if encoded, serialized, or passed to other systems.

[AbstractEnum]: Class_AbstractEnum.md
[AbstractAutoEnum]: Class_AbstractAutoEnum.md


## Simple Enum Comparison

The [AbstractEnum] base class
contains a `__toString()` implementation
that returns the instance's value,
typecast to a string.
This enables simple comparisons between
enum constants,
enum instances,
and raw values
even without knowing whether a given input value
is actually an enum instance or just a raw value:

```php
<?php
function ($triState) {
    TriState::validate($triState);
    /* Now we know that $triState is either a raw TriState enum value (i.e. just a string)
     * or maybe even a TriState instance (which has a value() getter and an equals() tester).
     * But thanks to __toString we can do simple comparions:  */
    if ($triState == TriState::HIGH) {
        // NB: This has to be a "==" comparison!
        // It won't work as expected with the type-safe "===" comparison.
        …
    }
}
```

Or course it would be much cleaner
to enforce explicit type safety through typehints:

```php
<?php
function (TriState $triState) {
    if ($triState->equals(TriState::HIGH)) {
        …
    }
}
```


## String Typecasting

[AbstractEnum]'s `__toString()` implementation
also allows Enum instances to be used in various scalar contexts.
One of these use cases is database storage.
Be it PDO or a database abstraction layer,
it's possible to include an enum instance in a query
without explicitly using its `value()` getter
because the instances will automatically be typecast to string.

Note that this might not work as expected
if your enum contains non-string values.
If your enum class contains, say,
both a `null` constant
and an `""` empty string constant,
both of them will be turned to the empty string `""`
when calling `__toString()`
because that is PHP's string representation of the `null` value.  
Similar undesirable things happen with boolean values.

```php
<?php
$stmtLog = $pdo->prepare('INSERT INTO log (timestamp, type) VALUES (NOW(), :logType)');

LogTypeEnum::validate($logType);
// No matter if $logType is a raw value or a LogTypeEnum instance,
// this will work (as long as LogTypeEnum contains only string constants):
$stmtLog->execute([':logType' => $logType]);
```


## Boolean Checks

Be careful with Enum classes containing boolean constants.

The instances are regular PHP objects
and will always be considered true,
it's just their values which can be tested easily:

```php
<?php
class MyBool extends \mle86\Enum\AbstractAutoEnum
{
    const YES   = true;
    const NO    = false;
    const UNDEF = null;
}

$myBool = new MyBool(…);

if ($myBool) {
  // WARNING: This is _always_ executed because $myBool is an object,
  // no matter which value it wraps!
}
if ($myBool->value()) {
  // This is the correct way to test boolean enum instances.
}
```
