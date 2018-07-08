<?php declare(strict_types=1);

namespace Test\Unit;

use PrepaidCard\Money;

class MoneyTest extends UnitTestCase
{
    public function testItShouldAdd()
    {
        $sut = new Money(3);
        $result = $sut->add(new Money(5));

        self::assertSame('8', $result->getAmount());
    }

    public function testItShouldSubtract()
    {
        $sut = new Money(3);
        $result = $sut->subtract(new Money(5));

        self::assertSame('-2', $result->getAmount());
    }

    public function testItShouldBeNegative()
    {
        $sut = new Money('-3');

        self::assertTrue($sut->isNegative());
    }

    public function testItShouldNotBeNegative()
    {
        $sut = new Money('3');

        self::assertFalse($sut->isNegative());
    }

    //public function testItShouldBeZero()
    //{
    //    $sut = new Money('0.00');
    //
    //    self::assertTrue($sut->isZero());
    //}
    //
    //public function testItShouldNotBeZero()
    //{
    //    $sut = new Money('0.01');
    //
    //    self::assertFalse($sut->isZero());
    //}
}
