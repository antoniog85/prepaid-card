<?php declare(strict_types=1);

namespace Test\Unit\Transaction;

use PrepaidCard\Money;
use PrepaidCard\Transaction\Transaction;
use Test\Unit\UnitTestCase;

class TransactionTest extends UnitTestCase
{
    public function testItShouldInstantiate()
    {
        $sut = new Transaction(
            'card-id',
            'merchant-id',
            new Money(23),
            'id'
        );

        self::assertSame('id', $sut->getTransactionId());
        self::assertSame('card-id', $sut->getCardId());
        self::assertSame('23', $sut->getAmount()->getAmount());
        self::assertSame('merchant-id', $sut->getMerchantId());
    }

    public function testItShouldAutoGenerateId()
    {
        $sut = new Transaction('card-id', 'merchant-id', new Money(23));

        self::assertNotNull($sut->getTransactionId());
    }

    public function testItShouldTransformToArray()
    {
        $sut = new Transaction(
            'card-id',
            'merchant-id',
            new Money(23),
            'id'
        );

        self::assertInternalType('array', $sut->toArray());
    }
}
