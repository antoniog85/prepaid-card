<?php declare(strict_types=1);

namespace Test\Unit\User\Ledger;

use PHPUnit\Framework\MockObject\MockObject;
use PrepaidCard\Transaction\Transaction;
use PrepaidCard\User\Ledger\LedgerManager;
use PrepaidCard\User\Ledger\LedgerRepositoryInterface;
use PrepaidCard\User\Ledger\Operation\LedgerOperationInterface;
use Test\Unit\UnitTestCase;

class LedgerManagerTest extends UnitTestCase
{
    /** @var MockObject */
    private $repository;

    /** @var LedgerManager */
    private $sut;

    public function setup()
    {
        $this->repository = $this->createMock(LedgerRepositoryInterface::class);
        $this->sut = new LedgerManager($this->repository);
    }

    public function testItShouldGetByCard()
    {
        $this->repository
            ->expects(self::once())
            ->method('getByCard');

        $this->sut->getByCard('id');
    }

    public function testItShouldGetCapturedAmount()
    {
        $this->repository
            ->expects(self::once())
            ->method('getCapturedAmount');

        $this->sut->getCapturedAmount('id');
    }

    public function testTransactionHasBeenRefunded()
    {
        $this->repository
            ->expects(self::once())
            ->method('transactionHasBeenRefunded');

        $this->sut->transactionHasBeenRefunded($this->createMock(Transaction::class));
    }

    public function testItShouldPerform()
    {
        $this->repository
            ->expects(self::once())
            ->method('create');

        $this->sut->perform($this->createMock(LedgerOperationInterface::class));
    }
}
