<?php

namespace mle86\Enum;

use mle86\Enum\Tests\Helper\Inheritance\BaseEnum;
use mle86\Enum\Tests\Helper\Inheritance\ExtendedEnum1;
use mle86\Enum\Tests\Helper\Inheritance\ExtendedEnum2;
use mle86\Enum\Tests\Helper\RestrictiveEnum;
use PHPUnit\Framework\TestCase;

/**
 * Tests {@see AbstractEnum}'s enum inheritance support.
 *
 * It uses the `Inheritance\` classes
 * for subclassing tests.
 */
class EnumSubclassTest extends TestCase
{

    public function testSameClassEquality(): void
    {
        $re1 = new RestrictiveEnum(RestrictiveEnum::ONLY_ALLOWED_VALUE);
        $re2 = new RestrictiveEnum(RestrictiveEnum::ONLY_ALLOWED_VALUE);

        $this->assertTrue($re1->equals($re2));
        $this->assertTrue($re2->equals($re1));
    }

    /**
     * Enum classes must accept subclass instances as valid (just like typehints do),
     * but they may not accept their subclass _values_ as valid
     * because they may not know about the subclass at all.
     *
     * @depends testSameClassEquality
     */
    public function testSubclassCompatibility(): void
    {
        $x1 = new ExtendedEnum1(ExtendedEnum1::X1);  // = 1004
        $y2 = new ExtendedEnum2(ExtendedEnum2::Y2);  // = 1004

        // Enum classes must accept subclass instances:
        $this->assertInstanceOf(BaseEnum::class, $x1);
        $this->assertInstanceOf(BaseEnum::class, $y2);
        $this->assertTrue(BaseEnum::isValid($x1));
        $this->assertTrue(BaseEnum::isValid($y2));
        // ...although their values are probably not considered valid.

        // Sibling instances are never valid nor equal, although their values might be:
        $this->assertFalse(ExtendedEnum1::isValid($y2));
        $this->assertFalse(ExtendedEnum2::isValid($x1));
        $this->assertFalse($x1->equals($y2));
        $this->assertFalse($y2->equals($x1));
    }

}
