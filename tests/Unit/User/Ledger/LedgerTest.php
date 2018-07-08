<?php declare(strict_types=1);

namespace Test\Unit\User\Ledger;

use PrepaidCard\Money;
use PrepaidCard\User\Ledger\Ledger;
use Test\Unit\UnitTestCase;

class LedgerTest extends UnitTestCase
{
    public function testItShouldInstantiate()
    {
        $sut = new Ledger(
            'transaction-id',
            new Money(2),
            new Money(3),
            new Money(4),
            'description',
            'id',
            new \DateTime('1985-9-10')
        );

        self::assertSame('id', $sut->getLedgerId());
        self::assertSame('transaction-id', $sut->getTransactionId());
        self::assertSame('2', $sut->getCredit()->getAmount());
        self::assertSame('3', $sut->getDebit()->getAmount());
        self::assertSame('4', $sut->getBlocked()->getAmount());
        self::assertSame('description', $sut->getDescription());
        self::assertInstanceOf(\DateTime::class, $sut->getDate());
        self::assertInternalType('array', $sut->toArray());
    }

    public function testItShouldAutoGenerateId()
    {
        $sut = new Ledger(
            'transaction-id',
            new Money(2),
            new Money(3),
            new Money(4)
        );

        self::assertNotNull($sut->getLedgerId());
    }
}
