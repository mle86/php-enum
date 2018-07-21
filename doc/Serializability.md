# Enum Serializability

[AbstractEnum]: Class_AbstractEnum.md
[AbstractAutoEnum]: Class_AbstractAutoEnum.md

[AbstractEnum] subclass instances
can be serialized with [serialize()](https://php.net/manual/function.serialize.php)
and unserialized with [unserialize()](https://php.net/manual/function.unserialize.php)
without issues,
both of which are built-in PHP functions.

Instances can also be JSON-serialized
with the built-in [json_encode()](https://php.net/manual/function.json-encode.php)
provided that their value itself is JSON-serializable
(which they ought to be:
[it is recommended to have only string/int/null enum constants](Type_Safety.md)).

Such JSON serializations of Enum instances
can of course be unserialized with [json_decode()](https://php.net/manual/function.json-decode.php).
This will however not return an Enum instance
but just their wrapped value:

```php
<?php
class TriState extends \mle86\Enum\AbstractAutoEnum
{
    public const HIGH  = 'H';
    public const LOW   = 'L';
    public const UNDEF = 'Z';
}

$triState = new TriState(TriState::HIGH);

$unserialized = unserialize(serialize($triState));
// This is now a second instance with the same value.

$jsonUnserialized = json_decode(json_encode($triState));
// This is now the string "H" (TriState::HIGH).
```
