<?php declare(strict_types=1);

namespace Test\Unit\Transaction;

use PHPUnit\Framework\MockObject\MockObject;
use PrepaidCard\Card\CardManager;
use PrepaidCard\Exception\OperationForbiddenException;
use PrepaidCard\Money;
use PrepaidCard\Transaction\TransactionManager;
use PrepaidCard\Transaction\TransactionService;
use PrepaidCard\User\Ledger\LedgerManager;
use PrepaidCard\User\Ledger\Operation\Debit\CaptureLedger;
use Test\Unit\UnitTestCase;

class TransactionServiceTest extends UnitTestCase
{
    /** @var MockObject */
    private $transactionManager;

    /** @var MockObject */
    private $cardManager;

    /** @var MockObject */
    private $ledgerManager;

    /** @var TransactionService */
    private $sut;

    public function setup()
    {
        $this->transactionManager = $this->createMock(TransactionManager::class);
        $this->cardManager = $this->createMock(CardManager::class);
        $this->ledgerManager = $this->createMock(LedgerManager::class);
        $this->sut = new TransactionService($this->transactionManager, $this->cardManager, $this->ledgerManager);
    }

    public function testItShouldGetByCard()
    {
        $this->transactionManager
            ->expects(self::once())
            ->method('getTransactionsByCard');

        $this->sut->getByCard('id');
    }

    public function testItShouldNotCaptureBecauseAmountGreaterThanAllowed()
    {
        $this->expectException(OperationForbiddenException::class);

        $this->ledgerManager
            ->method('getCapturedAmount')
            ->willReturn(new Money(11));

        $this->sut->capture('id', new Money(10));
    }

    public function testItShouldCapture()
    {
        $this->cardManager
            ->expects(self::once())
            ->method('debit');

        $this->ledgerManager
            ->expects(self::once())
            ->method('perform')
            ->with(self::isInstanceOf(CaptureLedger::class));

        $this->sut->capture('id', new Money(34));
    }

    public function testItShouldNotRefundBecauseAlreadyRefunded()
    {
        $this->expectException(OperationForbiddenException::class);

        $this->ledgerManager
            ->method('transactionHasBeenRefunded')
            ->willReturn(true);

        $this->sut->refund('id');
    }

    public function testItShouldRefund()
    {
        $this->cardManager
            ->expects(self::once())
            ->method('refund');

        $this->ledgerManager
            ->expects(self::once())
            ->method('perform');

        $this->sut->refund('id');
    }
}
