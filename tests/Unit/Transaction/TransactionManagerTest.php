<?php declare(strict_types=1);

namespace Test\Unit\Transaction;

use PHPUnit\Framework\MockObject\MockObject;
use PrepaidCard\Card\Card;
use PrepaidCard\Money;
use PrepaidCard\Transaction\TransactionManager;
use PrepaidCard\Transaction\TransactionRepositoryInterface;
use Test\Unit\UnitTestCase;

class TransactionManagerTest extends UnitTestCase
{
    /** @var MockObject */
    private $repository;

    /** @var TransactionManager */
    private $sut;

    public function setUp()
    {
        $this->repository = $this->createMock(TransactionRepositoryInterface::class);
        $this->sut = new TransactionManager($this->repository);
    }

    public function testItShoultGet()
    {
        $this->repository
            ->expects(self::once())
            ->method('get');

        $this->sut->get('id');
    }

    public function testItShouldGetTransactionsByCard()
    {
        $this->repository
            ->expects(self::once())
            ->method('getTransactionsByCard');

        $this->sut->getTransactionsByCard('id');
    }

    public function testItShouldPurchase()
    {
        $this->repository
            ->expects(self::once())
            ->method('persist');

        $this->sut->purchase(new Card('id'), 'id', new Money(3));
    }
}
