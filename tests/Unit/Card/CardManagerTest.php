<?php declare(strict_types=1);

namespace Test\Unit\Card;

use PHPUnit\Framework\MockObject\MockObject;
use PrepaidCard\Card\Card;
use PrepaidCard\Card\CardManager;
use PrepaidCard\Card\CardRepositoryInterface;
use PrepaidCard\Money;
use Test\Unit\UnitTestCase;

class CardManagerTest extends UnitTestCase
{
    /** @var MockObject */
    private $repository;

    /** @var MockObject */
    private $card;

    /** @var CardManager */
    private $sut;

    public function setUp()
    {
        $this->repository = $this->createMock(CardRepositoryInterface::class);
        $this->card = $this->createMock(Card::class);
        $this->sut = new CardManager($this->repository);
    }

    public function testItShouldPersist()
    {
        $this->repository
            ->expects(self::once())
            ->method('persist');

        $this->sut->persist($this->card);
    }

    public function testItShouldGet()
    {
        $this->repository
            ->expects(self::once())
            ->method('get');

        $this->sut->get('id');
    }

    public function testItShouldDebit()
    {
        $this->card
            ->expects(self::once())
            ->method('debit');

        $this->expectPersist();

        $this->sut->debit($this->card, new Money(2));
    }

    public function testItShouldCredit()
    {
        $this->card
            ->expects(self::once())
            ->method('credit');

        $this->expectPersist();

        $this->sut->credit($this->card, new Money(2));
    }

    public function testItShouldBlock()
    {
        $this->card
            ->expects(self::once())
            ->method('block');

        $this->expectPersist();

        $this->sut->block($this->card, new Money(2));
    }

    public function testItShouldRefund()
    {
        $this->card
            ->expects(self::once())
            ->method('refund');

        $this->expectPersist();

        $this->sut->refund($this->card, new Money(2));
    }

    private function expectPersist()
    {
        $this->repository
            ->expects(self::once())
            ->method('persist');
    }
}
