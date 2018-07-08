<?php declare(strict_types=1);

namespace Test\Unit\Card;

use PrepaidCard\Card\Card;
use PrepaidCard\Card\CardRepositoryInterface;
use PrepaidCard\Card\NotEnoughFundsException;
use PrepaidCard\Money;
use Test\Unit\UnitTestCase;

class CardTest extends UnitTestCase
{
    public function testItShouldInstantiateACard()
    {
        $sut = new Card(
            'user-id',
            new Money(23),
            new Money(45),
            'card-id'
        );

        self::assertSame('user-id', $sut->getOwnerId());
        self::assertSame('card-id', $sut->getCardId());
    }

    public function testItShouldAutoGenerateCardId()
    {
        $sut = new Card('user-id');

        self::assertNotNull($sut->getCardId());
    }

    public function testItShouldCredit()
    {
        $sut = new Card('user-id', new Money(23));
        $sut->credit(new Money(2));

        self::assertSame('25', $sut->toArray()[CardRepositoryInterface::FIELD_BALANCE]);
    }

    public function testItShouldNotCreditZeroAmount()
    {
        $this->expectException(\InvalidArgumentException::class);

        $sut = new Card('user-id', new Money(23));
        $sut->credit(new Money(0));
    }

    public function testItShouldNotCreditNegativeAmount()
    {
        $this->expectException(\InvalidArgumentException::class);

        $sut = new Card('user-id', new Money(23));
        $sut->credit(new Money(-13));
    }

    public function testItShouldDebit()
    {
        $sut = new Card('user-id', new Money(23), new Money(10));
        $sut->debit(new Money(2));

        self::assertSame('8', $sut->toArray()[CardRepositoryInterface::FIELF_BLOCKED]);
    }

    public function testItShouldNotDebitZeroAmount()
    {
        $this->expectException(\InvalidArgumentException::class);

        $sut = new Card('user-id', new Money(23), new Money(10));
        $sut->debit(new Money(0));
    }

    public function testItShouldNotDebitNegativeAmount()
    {
        $this->expectException(\InvalidArgumentException::class);

        $sut = new Card('user-id', new Money(23), new Money(10));
        $sut->debit(new Money(-2));
    }

    public function testItShouldNotDebitMoreThanAvailable()
    {
        $this->expectException(NotEnoughFundsException::class);

        $sut = new Card('user-id', new Money(23), new Money(10));
        $sut->debit(new Money(11));
    }

    public function testItShouldBlock()
    {
        $sut = new Card('user-id', new Money(23), new Money(10));
        $sut->block(new Money(2));

        self::assertSame('12', $sut->toArray()[CardRepositoryInterface::FIELF_BLOCKED]);
    }

    public function testItShouldNotBlockZeroAmount()
    {
        $this->expectException(\InvalidArgumentException::class);

        $sut = new Card('user-id', new Money(23));
        $sut->block(new Money(0));
    }

    public function testItShouldNotBlockNegativeAmount()
    {
        $this->expectException(\InvalidArgumentException::class);

        $sut = new Card('user-id', new Money(23));
        $sut->block(new Money(-2));
    }

    public function testItShouldNotBlockMoreThanAvailable()
    {
        $this->expectException(NotEnoughFundsException::class);

        $sut = new Card('user-id', new Money(23));
        $sut->block(new Money(24));
    }

    public function testItShouldRefund()
    {
        $sut = new Card('user-id', new Money(23));
        $sut->refund(new Money(2));

        self::assertSame('25', $sut->toArray()[CardRepositoryInterface::FIELD_BALANCE]);
    }
}
