<?php

namespace mle86\Enum;

/**
 * This base class contains a default {@see all()} implementation
 * that always returns the values of all public class constants.
 *
 * This makes writing usable enum classes very easy:
 * extend this class, put some constants in it, done.
 *
 * (Internally, it simply uses the {@see AutoEnumTrait}.)
 */
abstract class AbstractAutoEnum extends AbstractEnum
{
    use AutoEnumTrait;

}
